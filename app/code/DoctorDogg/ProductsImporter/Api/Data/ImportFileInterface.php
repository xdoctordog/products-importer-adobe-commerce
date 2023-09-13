<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api\Data;

/**
 * The interface that represents a file that is used to import products into Magento.
 */
interface ImportFileInterface
{
    /**
     * @const string ENTITY_ID
     */
    public const ENTITY_ID = 'entity_id';

    /**
     * @const string FILE_PATH
     */
    public const FILE_PATH = 'file_path';

    /**
     * @const string IS_PROCESSED
     */
    public const IS_PROCESSED = 'is_processed';

    /**
     * Get entity id
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

    /**
     * Get file path.
     *
     * @return string
     */
    public function getFilePath(): string;

    /**
     * Set file path.
     *
     * @param string $filePath
     * @return $this
     */
    public function setFilePath(string $filePath): self;

    /**
     * Get is processed.
     *
     * @return bool
     */
    public function getIsProcessed(): bool;

    /**
     * Set is processed.
     *
     * @param bool $isProcessed
     * @return $this
     */
    public function setIsProcessed(bool $isProcessed): self;
}
