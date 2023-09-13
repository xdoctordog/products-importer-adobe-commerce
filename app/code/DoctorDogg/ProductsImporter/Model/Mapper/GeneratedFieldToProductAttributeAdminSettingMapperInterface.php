<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Mapper;

/**
 * The interface which provides the possibility to map the fields using the auto generated field names
 * of the temporary table with buffer products and map them to the Admin Settings of the required fields
 * and custom product fields.
 */
interface GeneratedFieldToProductAttributeAdminSettingMapperInterface
{
    /**
     * Map fields and return prepared array with data.
     *
     * @return string[]
     */
    public function map(array $inputData): array;
}
