<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The interface which provides the information about the required Magento core product attributes.
 */
interface RequiredProductAttributesInterface
{
    /**
     * @const string SKU
     */
    public const SKU = 'sku';

    /**
     * @const string PRODUCT_TYPE
     */
    public const PRODUCT_TYPE = 'product_type';

    /**
     * @const string NAME
     */
    public const NAME = 'name';

    /**
     * @const string PRICE
     */
    public const PRICE = 'price';

    /**
     * @const string URL_KEY
     */
    public const URL_KEY = 'url_key';

    /**
     * @const string URL_KEY
     */
    public const _ATTRIBUTE_SET = '_attribute_set';

    /**
     * @const string[] const
     */
    public const _ = [
        self::SKU,
        self::PRODUCT_TYPE,
        self::NAME,
        self::PRICE,
        self::URL_KEY,
        self::_ATTRIBUTE_SET
    ];
}
