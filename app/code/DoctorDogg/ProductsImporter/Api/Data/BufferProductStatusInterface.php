<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api\Data;

/**
 * Auto-generated Magento class.
 */
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusExtensionInterface;
use \Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Interface for buffer product status which provides the table name and column's names and values for status of buffer product.
 * Status:
 *  d) CHECK PRODUCT EXISTS AFTER IMPORT: Check if the product exist after import process.
 */
interface BufferProductStatusInterface extends CustomAttributesDataInterface
{
    /**
     * @const string TABLE
     */
    public const TABLE = 'doctordogg_productsimporter_buffer_product_status';

    /**
     * @const string ENTITY_ID
     */
    public const ENTITY_ID = 'entity_id';

    /**
     * @const string BUFFER_PRODUCT_ID
     */
    public const BUFFER_PRODUCT_ID = 'buffer_product_id';

    /**
     * @const string PRODUCT_EXISTS_AFTER_IMPORT_STATUS_KEY
     */
    public const PRODUCT_EXISTS_AFTER_IMPORT_STATUS_KEY = 'product_exists_after_import_status';

    /**
     * @const string VALIDATION_ERRORS_KEY
     */
    public const VALIDATION_ERRORS_KEY = 'validation_errors';

    /**
     * d) CHECK PRODUCT EXISTS AFTER IMPORT.
     */

    /**
     * @const array CHECK_PRODUCT_EXISTS_AFTER_IMPORT
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT = [
        /**
         * Default value in db table.
         */
        self::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED,

        self::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS,
        self::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST,
        self::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED
    ];

    /**
     * Undefined status.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED = 0;

    /**
     * Text with undefined status for the grid.
     *
     * @const string CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED_STRING
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED_STRING = 'UNDEFINED';

    /**
     * Product is checked after import and product exists.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS = 1;

    /**
     * Text for grid with product is checked after import and product exists.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS_STRING
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS_STRING = 'EXISTS';

    /**
     * Product is checked after import and product does not exist.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST = 2;

    /**
     * Text for grid with product is checked after import and product does not exist.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST_STRING
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST_STRING = 'DOES NOT EXIST';

    /**
     * Product is not checked after import, and we do not know if product exists.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED = 3;

    /**
     * Text for grid with product is not checked after import, and we do not know if product exists.
     *
     * @const int CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED_STRING
     */
    public const CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED_STRING = 'NOT VERIFIED';

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
     * Get buffer product status id.
     *
     * @return int
     */
    public function getBufferProductId(): int;

    /**
     * Set buffer product status id.
     *
     * @param int $bufferProductId
     * @return $this
     */
    public function setBufferProductId(int $bufferProductId): self;

    /**
     * Get product exists after import status.
     *
     * @return int
     */
    public function getProductExistsAfterImportStatus(): int;

    /**
     * Set product exists after import status.
     *
     * @param int $productExistsAfterImportStatus
     * @return $this
     */
    public function setProductExistsAfterImportStatus(int $productExistsAfterImportStatus): self;

    /**
     * Get validation errors.
     *
     * @return string[]
     */
    public function getValidationErrors(): array;

    /**
     * Set validation errors.
     *
     * @param string[] $validationErrors
     * @return $this
     */
    public function setValidationErrors(array $validationErrors): self;

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * CAUTION! The next return line should contain the full name of extension interface
     *
     * @return \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param BufferProductStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(BufferProductStatusExtensionInterface $extensionAttributes);
}
