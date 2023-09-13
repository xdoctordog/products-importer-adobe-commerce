<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Mapper;

use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;

/**
 * Mapper to transform the int code of the 'product_exists_after_import_status' to text on the grid.
 */
class BufferProductStatusProductExistsAfterImportStatusCodeToText
{
    /**
     * Map.
     *
     * @param int $code
     * @return string
     */
    public function map(int $code): string
    {
        $codeString = match ($code) {
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED
                => BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED_STRING,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS
                => BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS_STRING,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST
                => BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST_STRING,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED
                => BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED_STRING,
            default => '',
        };

        return (string)$codeString;
    }
}
