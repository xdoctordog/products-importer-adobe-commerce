<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Provider;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;

use DoctorDogg\ProductsImporter\Api\BufferProductProviderInterface;

/**
 * Repository.
 */
use DoctorDogg\ProductsImporter\Api\BufferProductInterfaceRepositoryInterface;

/**
 * Model.
 */
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterfaceFactory;

/**
 * Buffer product provider.
 */
class BufferProductProvider implements BufferProductProviderInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var BufferProductInterfaceRepositoryInterface
     */
    private $bufferProductInterfaceRepositoryInterface;

    /**
     * @var BufferProductInterfaceFactory
     */
    private $bufferProductInterfaceFactory;

    /**
     * Constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepositoryInterface
     * @param BufferProductInterfaceFactory $bufferProductInterfaceFactory
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepositoryInterface,
        BufferProductInterfaceFactory $bufferProductInterfaceFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->bufferProductInterfaceRepositoryInterface = $bufferProductInterfaceRepositoryInterface;
        $this->bufferProductInterfaceFactory = $bufferProductInterfaceFactory;
    }

    /**
     * Get buffer product interface entities.
     *
     * @return array
     */
    public function getBufferProductInterfaceEntities(): array
    {
        $id = 1;//@todo: Example of filter.
        /**
         * @var SortOrder
         */
        $sortOrder = $this->sortOrderBuilder->setField(BufferProductInterface::ENTITY_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(BufferProductInterface::ENTITY_ID, $id)
            ->addSortOrder($sortOrder)
            ->create();
        $list = $this->bufferProductInterfaceRepositoryInterface->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * @neverused
     *
     * Get buffer product interface entities by ID.
     *
     * @param int $bufferProductInterfaceId
     * @return array
     */
    public function getBufferProductInterfaceEntitiesByEntityId(int $bufferProductInterfaceId): array
    {
        /**
         * @var SortOrder $sortOrder
         */
        $sortOrder = $this->sortOrderBuilder->setField(BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID)
            ->setDirection(SortOrder::SORT_DESC)
            ->create();
        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(BufferProductInterface::ENTITY_ID, $bufferProductInterfaceId)
            ->addSortOrder($sortOrder)
            ->create();
        $list = $this->bufferProductInterfaceRepositoryInterface->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * Get buffer product interface entities which were not planned for import previously.
     *
     * @return array
     */
    public function getBufferProductInterfaceEntitiesNotPlannedForImport(): array
    {
        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID, $isPlannedForImport = true, 'neq')
            ->create();
        $list = $this->bufferProductInterfaceRepositoryInterface->getList($searchCriteria);

        return $list->getItems();
    }

    /**
     * Get buffer product interface entities which were not imported previously and not planned for importing previously also.
     *
     * @param int $numberProductsScheduledAtTime
     * @return array
     */
    public function getBufferProductInterfaceEntitiesNotImportedNotPlannedForImport(int $numberProductsScheduledAtTime): array
    {
        if ($numberProductsScheduledAtTime < 0) {
            $numberProductsScheduledAtTime = 0;
        }

        /**
         * @todo: Have the idea to add the column with created_at and sort by it,
         * @todo: then limiting only firstly added products
         * @todo: But we are going to use the entity_id for this purpose.
         */
        /**
         * @var SearchCriteria $searchCriteria
         */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(BufferProductInterface::IS_ALREADY_IMPORTED_KEY, $isAlreadyImported = true, 'neq')
            ->addFilter(BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID, $isPlannedForImport = true, 'neq');
        $this->searchCriteriaBuilder
            ->setPageSize($numberProductsScheduledAtTime);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->bufferProductInterfaceRepositoryInterface->getList($searchCriteria);

        return $list->getItems();
    }
}
