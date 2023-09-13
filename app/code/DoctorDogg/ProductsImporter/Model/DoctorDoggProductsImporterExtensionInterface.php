<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

/**
 * The representation of the current extension:
 *  a) name
 *  b) prefix
 */
interface DoctorDoggProductsImporterExtensionInterface
{
    /**
     * DoctorDogg ProductsImporter extension name.
     *
     * @const string NAME
     */
    public const NAME = 'DoctorDogg_ProductsImporter';

    /**
     * @const string PREFIX
     */
    public const PREFIX = 'doctordogg-productsimporter-';
}
