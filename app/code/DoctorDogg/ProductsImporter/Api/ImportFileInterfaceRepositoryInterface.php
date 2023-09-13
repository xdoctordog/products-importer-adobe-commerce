<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchResultsInterface;
use \Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Framework\Exception\StateException;
use \Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface of the model.
 */
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;

/**
 * Resource model.
 */
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile as ImportFileResourceModel;

/**
 * Repository interface for import file interface.
 */
interface ImportFileInterfaceRepositoryInterface
{
    /**
     * Save import file interface.
     *
     * @param ImportFileInterface $importFileInterface
     * @return ImportFileResourceModel
     * @throws AlreadyExistsException
     */
    public function save(ImportFileInterface $importFileInterface): ImportFileResourceModel;

    /**
     * Get import file interface by import file interface id.
     *
     * @param int $importFileInterfaceId
     * @return ImportFileInterface
     * @throws NoSuchEntityException
     */
    public function get(int $importFileInterfaceId): ImportFileInterface;

    /**
     * Delete import file interface.
     *
     * @param ImportFileInterface $importFileInterface
     * @return bool Will returned True if deleted
     */
    public function delete(ImportFileInterface $importFileInterface): bool;

    /**
     * Delete import file interface by id.
     *
     * @param int $importFileInterfaceId
     * @return bool Will returned True if deleted
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function deleteById(int $importFileInterfaceId): bool;

    /**
     * Get list of import file interfaces.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * Get file import interface entities by file path.
     *
     * @param string $filePath
     * @param bool $processedOnly
     * @return array
     */
    public function getFileImportInterfaceEntitiesByFilePath(string $filePath, bool $processedOnly = false): array;

    /**
     * Get file import interface entities which are not processed.
     *
     * @return array
     */
    public function getFileImportInterfaceEntitiesNotProcessed(): array;
}
