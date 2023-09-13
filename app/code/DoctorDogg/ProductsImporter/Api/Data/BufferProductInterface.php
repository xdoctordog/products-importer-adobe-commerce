<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api\Data;

/**
 * Auto-generated Magento class.
 */
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductExtensionInterface;
use \Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * The interface which represents the intermediate entity of the buffer product.
 */
interface BufferProductInterface extends CustomAttributesDataInterface
{
    /**
     * @const string TABLE
     */
    public const TABLE = 'doctordogg_productsimporter_buffer_product';

    /**
     * @const string ENTITY_ID
     */
    public const ENTITY_ID = 'entity_id';

    /**
     * @const string IS_PLANNED_FOR_IMPORT_ID
     */
    public const IS_PLANNED_FOR_IMPORT_ID = 'doctor_dogg_is_planned_for_import';

    /**
     * This field should default to zero.
     * And after the product is imported into Magento, it should be set to one.
     *
     * @const string IS_ALREADY_IMPORTED_KEY
     */
    public const IS_ALREADY_IMPORTED_KEY = 'doctor_dogg_is_already_imported';

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
     * Get is planned for import.
     *
     * @return bool
     */
    public function getIsPlannedForImport(): bool;

    /**
     * Set is planned for import.
     *
     * @param bool $isPlannedForImport
     * @return $this
     */
    public function setIsPlannedForImport(bool $isPlannedForImport): self;

    /**
     * Get is already imported.
     *
     * @return bool
     */
    public function getIsAlreadyImported(): bool;

    /**
     * Set is already imported.
     *
     * @param bool $isAlreadyImported
     * @return $this
     */
    public function setIsAlreadyImported(bool $isAlreadyImported): self;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * CAUTION! The next return line should contain the full name of extension interface
     *
     * @return \DoctorDogg\ProductsImporter\Api\Data\BufferProductExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param BufferProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(BufferProductExtensionInterface $extensionAttributes);
}
