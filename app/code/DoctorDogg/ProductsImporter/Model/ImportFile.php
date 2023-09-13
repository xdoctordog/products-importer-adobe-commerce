<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Framework\Model\AbstractModel;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile as ImportFileResource;

/**
 * The class that represents a file that is used to import products into Magento.
 */
class ImportFile extends AbstractModel implements ImportFileInterface
{
    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(ImportFileResource::class);
    }

    /**
     * Get entity id
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(self::ENTITY_ID);
    }

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self
    {
        $this->setData(self::ENTITY_ID, $entityId);
        return $this;
    }

    /**
     * Get file path.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return (string) $this->_getData(self::FILE_PATH);
    }

    /**
     * Set file path.
     *
     * @param string $filePath
     * @return $this
     */
    public function setFilePath(string $filePath): self
    {
        $this->setData(self::FILE_PATH, $filePath);
        return $this;
    }

    /**
     * Get is processed.
     *
     * @return bool
     */
    public function getIsProcessed(): bool
    {
        return (bool) $this->_getData(self::IS_PROCESSED);
    }

    /**
     * Set is processed.
     *
     * @param bool $isProcessed
     * @return $this
     */
    public function setIsProcessed(bool $isProcessed): self
    {
        $this->setData(self::IS_PROCESSED, $isProcessed);
        return $this;
    }
}
