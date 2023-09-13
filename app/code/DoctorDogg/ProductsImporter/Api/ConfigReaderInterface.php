<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * Interface which allows reading of config values.
 */
interface ConfigReaderInterface
{
    /**
     * Get previous state product additional attributes.
     * @deprecated
     *
     * @return string|null
     */
    public function getPreviousStateProductAdditionalAttributes(): ?string;

    /**
     * Get status about if we are going to remove previously added custom attributes.
     *
     * @return bool|null
     */
    public function getRemovePreviouslyAddedCustomAttributes(): ?bool;

    /**
     * Get switcher
     *
     * @return bool|null
     */
    public function getImportKnifeSwitcher(): ?bool;
}
