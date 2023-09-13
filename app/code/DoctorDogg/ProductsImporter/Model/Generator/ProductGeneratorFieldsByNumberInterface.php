<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Generator;

/**
 * Interface that provides possibility to generate temporary product's fields by its number.
 */
interface ProductGeneratorFieldsByNumberInterface
{
    /**
     * Generate fields.
     *
     * @param int $number
     * @return array[]
     */
    public function generate(int $number): array;
}
