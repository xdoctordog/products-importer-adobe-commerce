<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field;

/**
 * The interface contains field's key's name of the required Magento core product attributes.
 */
interface MagentoCoreProductAttributeInterface
{
    /**
     * @const string REQUIRED_PRODUCT_ATTRIBUTE
     */
    public const REQUIRED_PRODUCT_ATTRIBUTE = 'required_product_attribute';

    /**
     * @const string REQUIRED_PRODUCT_ATTRIBUTE_POSITION_IMPORT_FILE
     */
    public const REQUIRED_PRODUCT_ATTRIBUTE_POSITION_IMPORT_FILE = 'required_product_attribute_position_import_file';

    /**
     * @const string REQUIRED_PRODUCT_ATTRIBUTE_DEFAULT_VALUE
     */
    public const REQUIRED_PRODUCT_ATTRIBUTE_DEFAULT_VALUE = 'required_product_attribute_default_value';
}
