<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use Magento\Framework\Api\SearchCriteria;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use \Magento\Framework\Api\SearchResults;
use \Magento\Framework\Api\SearchResultsFactory;
use \Magento\Framework\Api\SearchResultsInterface;
use \Magento\Framework\Api\SortOrder;
use \Magento\Framework\Api\SortOrderBuilder;
use \Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Exception\StateException;
use \Psr\Log\LoggerInterface;

/**
 * Model.
 */
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface as ModelInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory as InterfaceFactory;

/**
 * Resource model.
 */
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile as ImportFileResourceModel;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile as ResourceModel;

/**
 * Collection.
 */
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile\Collection;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile\CollectionFactory;

/**
 * Repository interface.
 */
use \DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;

/**
 * Repository for import file interface.
 */
class ImportFileInterfaceRepository implements ImportFileInterfaceRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private ResourceModel $resourceModel;

    /**
     * @var InterfaceFactory
     */
    private InterfaceFactory $interfaceFactory;

    /**
     * @var SearchResultsFactory
     */
    private SearchResultsFactory $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var SortOrderBuilder
     */
    private SortOrderBuilder $sortOrderBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * Constructor.
     *
     * @param ResourceModel $resourceModel
     * @param InterfaceFactory $interfaceFactory
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceModel $resourceModel,
        InterfaceFactory $interfaceFactory,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchResultsFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        LoggerInterface $logger
    ) {
        $this->resourceModel = $resourceModel;
        $this->interfaceFactory = $interfaceFactory;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->logger = $logger;
    }

    /**
     * Save import file interface.
     *
     * @param ModelInterface $object
     * @return ResourceModel
     * @throws AlreadyExistsException
     */
    public function save(ModelInterface $object): ResourceModel
    {
        return $this->resourceModel->save($object);
    }

    /**
     * Get import file interface by import file interface id.
     *
     * @param int $objectId
     * @return ModelInterface
     * @throws NoSuchEntityException
     */
    public function get(int $objectId): ModelInterface
    {
        $object = $this->interfaceFactory->create();
        $object->load($objectId);

        return $object;
    }

    /**
     * Delete import file interface.
     *
     * @param ModelInterface $object
     * @return bool Will returned true if deleted
     */
    public function delete(ModelInterface $object): bool
    {
        try {
            $this->resourceModel->delete($object);
        } catch (\Throwable $throwable) {
            $this->logger->info(
                'File: ' . $throwable->getFile() . ' '
                . 'on the line: ' . $throwable->getLine() . ' ' .
                $throwable->getMessage()
            );
            return false;
        }

        return true;
    }

    /**
     * Delete import file interface by id.
     *
     * @param int $importFileInterfaceId
     * @return bool Will returned True if deleted
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById(int $importFileInterfaceId): bool
    {
        $object = $this->get($importFileInterfaceId);
        return $this->delete($object);
    }

    /**
     * Get list of buffer product interfaces.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);
        $items = $collection->getItems();

        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * Get file import interface entities by file path.
     *
     * @param string $filePath
     * @param bool $processedOnly
     * @return array
     */
    public function getFileImportInterfaceEntitiesByFilePath(
        string $filePath,
        bool $processedOnly = false
    ): array {
        /**
         * @var SortOrder
         */
        $sortOrder = $this->sortOrderBuilder->setField(ModelInterface::ENTITY_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        $this->searchCriteriaBuilder
            ->addFilter(ModelInterface::FILE_PATH, $filePath)
            ->addSortOrder($sortOrder);

        if ($processedOnly) {
            $this->searchCriteriaBuilder->addFilter(ModelInterface::IS_PROCESSED, $processedOnly);
        }

        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $list = $this->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * Get file import interface entities which are not processed.
     *
     * @return array
     */
    public function getFileImportInterfaceEntitiesNotProcessed(): array
    {
        /**
         * @var SortOrder
         */
        $sortOrder = $this->sortOrderBuilder->setField(ModelInterface::ENTITY_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();

        $this->searchCriteriaBuilder
            ->addFilter(ModelInterface::IS_PROCESSED, $processedOnly = false)
            ->addSortOrder($sortOrder);

        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $list = $this->getList($searchCriteria);

        return $list->getItems();
    }
}
