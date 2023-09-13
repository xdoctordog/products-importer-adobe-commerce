<?php

declare(strict_types=1);

namespace DoctorDogg\FictionalProductsGenerator\Api;

/**
 * Interface for products generating.
 */
interface ProductsGeneratorInterface
{
    /**
     * Genarate fictional products.
     *
     * @return array
     */
    public function generate(): array;
}
