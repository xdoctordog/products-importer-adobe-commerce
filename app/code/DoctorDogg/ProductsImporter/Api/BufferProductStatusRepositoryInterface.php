<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

use DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface as ModelInterface;
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus as BufferProductResourceModel;

/**
 * Repository interface for buffer product statuses.
 */
interface BufferProductStatusRepositoryInterface
{
    /**
     * Save buffer product status.
     *
     * @param ModelInterface $model
     * @return BufferProductResourceModel
     * @throws AlreadyExistsException
     */
    public function save(ModelInterface $model): BufferProductResourceModel;

    /**
     * Get buffer product interface by buffer product interface id.
     *
     * @param int $id
     * @return ModelInterface
     */
    public function get(int $id): ModelInterface;

    /**
     * Delete buffer product interface.
     *
     * @param ModelInterface $model
     * @return bool Will returned True if deleted
     */
    public function delete(ModelInterface $model): bool;

    /**
     * Delete buffer product interface by id.
     *
     * @param int $id
     * @return bool Will returned True if deleted
     */
    public function deleteById(int $id): bool;

    /**
     * Get list of buffer product interfaces.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Get buffer product status entities by buffer product ID.
     *
     * @param int $bufferProductId
     * @return array
     */
    public function getByBufferProductId(int $bufferProductId): array;

    /**
     * Get only one buffer product status by buffer product ID.
     *
     * @param int $bufferProductId
     * @return ModelInterface|null
     */
    public function getOneByBufferProductId(int $bufferProductId): ?ModelInterface;

    /**
     * Delete buffer product status entities by buffer product ID.
     *
     * @param int $bufferProductId
     * @return bool
     */
    public function deleteByBufferProductId(int $bufferProductId): bool;
}
