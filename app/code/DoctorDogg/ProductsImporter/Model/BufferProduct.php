<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Framework\Model\AbstractExtensibleModel;
/**
 * Auto-generated Magento class.
 */
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductExtensionInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct as BufferProductResource;

/**
 * The class which represents the intermediate entity of the buffer product.
 */
class BufferProduct extends AbstractExtensibleModel implements BufferProductInterface
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'doctordogg_productsimporter_buffer_product';

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(BufferProductResource::class);
    }

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(BufferProductInterface::ENTITY_ID);
    }

    /**
     * Set entity id.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self
    {
        $this->setData(BufferProductInterface::ENTITY_ID, $entityId);
        return $this;
    }

    /**
     * Get is planned for import.
     *
     * @return bool
     */
    public function getIsPlannedForImport(): bool
    {
        return (bool) $this->_getData(BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID);
    }

    /**
     * Set is planned for import.
     *
     * @param bool $isPlannedForImport
     * @return $this
     */
    public function setIsPlannedForImport(bool $isPlannedForImport): self
    {
        $this->setData(BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID, $isPlannedForImport);
        return $this;
    }

    /**
     * Get is already imported.
     *
     * @return bool
     */
    public function getIsAlreadyImported(): bool
    {
        return (bool) $this->_getData(BufferProductInterface::IS_ALREADY_IMPORTED_KEY);
    }

    /**
     * Set is already imported.
     *
     * @param bool $isAlreadyImported
     * @return $this
     */
    public function setIsAlreadyImported(bool $isAlreadyImported): self
    {
        $this->setData(BufferProductInterface::IS_ALREADY_IMPORTED_KEY, $isAlreadyImported);
        return $this;
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \DoctorDogg\ProductsImporter\Api\Data\BufferProductExtensionInterface|null
     */
    public function getExtensionAttributes(): BufferProductExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param BufferProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(BufferProductExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
