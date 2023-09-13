<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The interface which provides the info about
 *  - name of the group for admin panel product edit page
 *  - key of the ???
 */
interface ProductAttributeAdminSettingInterface
{
    /**
     * @const string EXTENSION_PREFIX
     */
    public const EXTENSION_PREFIX = 'doctor_dogg_products_importer_';

    /**
     * @const string GROUP_NAME
     */
    public const GROUP_NAME = 'Doctor Dogg Products Importer';

    /**
     * @const string PRODUCT_ATTRIBUTE_CODE_KEY
     */
    public const PRODUCT_ATTRIBUTE_CODE_KEY = 'product_attribute_code';

    /**
     * @const string PRODUCT_ATTRIBUTE_DESCRIPTION_KEY
     */
    public const PRODUCT_ATTRIBUTE_DESCRIPTION_KEY = 'product_attribute_description';

    /**
     * @const string PRODUCT_ATTRIBUTE_DESCRIPTION
     */
    public const PRODUCT_ATTRIBUTE_DESCRIPTION = 'product_attribute_description';

    /**
     * @const string PRODUCT_ATTRIBUTE_POSITION
     */
    public const PRODUCT_ATTRIBUTE_POSITION = 'product_attribute_position_import_file';

    /**
     * @const string PRODUCT_ATTRIBUTE_DEFAULT_VALUE
     */
    public const PRODUCT_ATTRIBUTE_DEFAULT_VALUE = 'product_attribute_default_value';
}
