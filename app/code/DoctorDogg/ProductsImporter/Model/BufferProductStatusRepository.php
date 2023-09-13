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
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Psr\Log\LoggerInterface;

use \Magento\Framework\Api\SortOrder;
use \Magento\Framework\Api\SortOrderBuilder;
use \Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Log preparer.
 */
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * Model.
 */
use DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface as ModelInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterfaceFactory as ModelInterfaceFactory;

/**
 * Resource model.
 */
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus as ResourceModel;

/**
 * Collection.
 */
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus\Collection;
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus\CollectionFactory;

/**
 * Repository interface.
 */
use \DoctorDogg\ProductsImporter\Api\BufferProductStatusRepositoryInterface as RepositoryInterface;

/**
 * Repository for the buffer product statuses.
 */
class BufferProductStatusRepository implements RepositoryInterface
{
    /**
     * @var ModelInterfaceFactory
     */
    private $modelFactory;

    /**
     * @var ResourceModel
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
     * @param ModelInterfaceFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param SearchResultsFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param SortOrderBuilder $sortOrderBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     */
    public function __construct(
        ModelInterfaceFactory $modelFactory,
        ResourceModel $resourceModel,
        SearchResultsFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        SortOrderBuilder $sortOrderBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparerInterface
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
    }

    /**
     * Save buffer product status.
     *
     * @param ModelInterface $model
     * @return ResourceModel
     * @throws AlreadyExistsException
     */
    public function save(ModelInterface $model): ResourceModel
    {
        return $this->resourceModel->save($model);
    }

    /**
     * Get model object by id.
     *
     * @param int $id
     * @return ModelInterface
     */
    public function get(int $id): ModelInterface
    {
        $model = $this->modelFactory->create();
        $model->load($id);

        return $model;
    }

    /**
     * Delete model object.
     *
     * @param ModelInterface $model
     * @return bool Will returned True if deleted
     */
    public function delete(ModelInterface $model): bool
    {
        try {
            $this->resourceModel->delete($model);
        } catch (\Throwable $throwable) {
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));

            return false;
        }

        return true;
    }

    /**
     * Delete model object by id.
     *
     * @param int $id
     * @return bool Will returned True if deleted
     */
    public function deleteById(int $id): bool
    {
        $model = $this->get($id);
        return $this->delete($model);
    }

    /**
     * Get list of model objects.
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

        $this->collectionProcessor->process($searchCriteria, $collection);
        $items = $collection->getItems();

        $searchResult->setItems($items);
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * Get buffer product status entities by buffer product ID.
     *
     * @param int $bufferProductId
     *
     * @return ModelInterface[]
     */
    public function getByBufferProductId(int $bufferProductId): array
    {
        /**
         * @var SortOrder
         */
        $sortOrder = $this->sortOrderBuilder->setField(ModelInterface::ENTITY_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        $this->searchCriteriaBuilder
            ->addFilter(ModelInterface::BUFFER_PRODUCT_ID, $bufferProductId)
            ->addSortOrder($sortOrder);
        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $list = $this->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * Get only one buffer product status by buffer product ID.
     *
     * @param int $bufferProductId
     * @return ModelInterface|null
     */
    public function getOneByBufferProductId(int $bufferProductId): ?ModelInterface
    {
        $items = $this->getByBufferProductId($bufferProductId);

        if (\count($items) <= 0) {
            return null;
        }

        return \current($items);
    }

    /**
     * Delete buffer product status entities by buffer product ID.
     *
     * @param int $bufferProductId
     * @return bool
     */
    public function deleteByBufferProductId(int $bufferProductId): bool
    {
        $deleteResult = false;
        $items = $this->getByBufferProductId($bufferProductId);
        if (\is_iterable($items) && \count($items)) {
            $isFirstTime = true;
            foreach ($items as $item) {
                if ($isFirstTime) {
                    $isFirstTime = false;
                    $deleteResult = true;
                }
                $deleteResult &= $this->delete($item);
            }
        }

        return (bool)$deleteResult;
    }
}
