<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * Interface which allows gets the previous state of the additional attributes of the product and then decide
 * which attributes should be removed from the Product entity and which should be added and adds them.
 */
interface NewProductAttributesProcessorInterface
{
    /**
     * Process:
     *  - Get previously added product attributes from the config
     *
     * @param array $newCustomProductAttributes
     * @param array $oldArrayProductAttributes
     * @return void
     */
    public function process(array $newCustomProductAttributes, array $oldArrayProductAttributes);
}
