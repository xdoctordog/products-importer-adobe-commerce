<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface model.
 */
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;

/**
 * Resource model.
 */
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct as BufferProductResourceModel;

/**
 * Repository interface for buffer product interface.
 */
interface BufferProductInterfaceRepositoryInterface
{
    /**
     * Save buffer product interface.
     *
     * @param BufferProductInterface $bufferProductInterface
     * @return BufferProductResourceModel
     * @throws AlreadyExistsException
     */
    public function save(BufferProductInterface $bufferProductInterface): BufferProductResourceModel;

    /**
     * Get buffer product interface by buffer product interface id.
     *
     * @param int $bufferProductInterfaceId
     * @return BufferProductInterface
     */
    public function get(int $bufferProductInterfaceId): BufferProductInterface;

    /**
     * Delete buffer product interface.
     *
     * @param BufferProductInterface $bufferProductInterface
     * @return bool Will returned True if deleted
     */
    public function delete(BufferProductInterface $bufferProductInterface): bool;

    /**
     * Delete buffer product interface by id.
     *
     * @param int $bufferProductInterfaceId
     * @return bool Will returned True if deleted
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById(int $bufferProductInterfaceId): bool;

    /**
     * Get list of buffer product interfaces.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
