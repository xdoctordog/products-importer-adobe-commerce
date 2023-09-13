<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * Interface which is getting the info with
 *  - new array of product attributes
 *  - previously added custom product attributes
 *
 * The attributes which are in the array of previously added custom product attributes
 * and missed in the new array of product attributes - will be deleted
 *  a) if we didn't set the config setting "Don't delete the previously added attributes"
 *  b) :)
 */
interface ProductAttributesUpdaterInterface
{
    /**
     * Update product attributes.
     *
     * @param array|null $newCustomProductAttributes
     * @param array|null $previouslyAddedCustomProductAttributes
     * @return mixed
     */
    public function update(?array $newCustomProductAttributes, ?array $previouslyAddedCustomProductAttributes): bool;
}
