<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Api\SearchResultsFactory;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Psr\Log\LoggerInterface;

/**
 * Log preparer.
 */
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * Model.
 */
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterfaceFactory;

/**
 * Resource model.
 */
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct as BufferProductResourceModel;

/**
 * Collection.
 */
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct\Collection;
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct\CollectionFactory;

/**
 * Repository interface.
 */
use \DoctorDogg\ProductsImporter\Api\BufferProductInterfaceRepositoryInterface;

/**
 * Repository class for buffer product.
 */
class BufferProductInterfaceRepository implements BufferProductInterfaceRepositoryInterface
{
    /**
     * @var BufferProductResourceModel
     */
    private $resourceModel;

    /**
     * @var SearchResultsFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var BufferProductInterfaceFactory
     */
    private $bufferProductInterfaceFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * Constructor
     *
     * @param BufferProductResourceModel $resourceModel
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param BufferProductInterfaceFactory $bufferProductInterfaceFactory
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        BufferProductResourceModel $resourceModel,
        SearchResultsFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        BufferProductInterfaceFactory $bufferProductInterfaceFactory,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparerInterface,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->bufferProductInterfaceFactory = $bufferProductInterfaceFactory;
        $this->logger = $logger;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
    }

    /**
     * Save buffer product interface.
     *
     * @param BufferProductInterface $bufferProductInterface
     * @return BufferProductResourceModel
     * @throws AlreadyExistsException
     */
    public function save(BufferProductInterface $bufferProductInterface): BufferProductResourceModel
    {
        return $this->resourceModel->save($bufferProductInterface);
    }

    /**
     * Get buffer product interface by buffer product interface id.
     *
     * @param int $bufferProductInterfaceId
     * @return BufferProductInterface
     */
    public function get(int $bufferProductInterfaceId): BufferProductInterface
    {
        $bufferProductInterface = $this->bufferProductInterfaceFactory->create();
        $bufferProductInterface->load($bufferProductInterfaceId);

        return $bufferProductInterface;
    }

    /**
     * Delete buffer product interface.
     *
     * @param BufferProductInterface $bufferProductInterface
     * @return bool Will returned true if deleted
     */
    public function delete(BufferProductInterface $bufferProductInterface): bool
    {
        try {
            $this->resourceModel->delete($bufferProductInterface);
        } catch (\Throwable $throwable) {
            /**
             * @TODO: We should decide if we should to log or do something else.
             */
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));

            return false;
        }

        return true;
    }

    /**
     * Delete buffer product interface by id.
     *
     * @param int $bufferProductInterfaceId
     * @return bool Will returned True if deleted
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById(int $bufferProductInterfaceId): bool
    {
        $bufferProductInterface = $this->get($bufferProductInterfaceId);
        return $this->delete($bufferProductInterface);
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
        $this->extensionAttributesJoinProcessor->process($collection);

        $items = $collection->getItems();

        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }
}
