<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Guarantee;

use \DoctorDogg\ProductsImporter\Api\ProductAttributeAdminSettingInterface;

/**
 * Guarantee that all necessary fields are presented in the elements of the array.
 */
class ProductAdditionalAttributesGuarantee
{
    /**
     * @const array ITEMS
     */
    public const REQUIRED_ITEMS = [
        ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_CODE_KEY,
        ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DESCRIPTION_KEY,
        ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_POSITION,
        ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DEFAULT_VALUE
    ];

    /**
     * @const string DEFAULT_VALUE
     */
    public const DEFAULT_VALUE = null;

    /**
     * Guarantee all necessary fields for the item (additional product attribute) in the admin settings.
     *
     * @param array $arr
     * @return array
     */
    public function guarantee(array $arr): array
    {
        foreach ($arr as $key => $arrItem) {
            foreach (static::REQUIRED_ITEMS as $itemKey) {
                if (!isset($arrItem[$itemKey])) {
                    $arr[$key][$itemKey] = static::DEFAULT_VALUE;
                }
            }
        }

        return $arr;
    }
}
