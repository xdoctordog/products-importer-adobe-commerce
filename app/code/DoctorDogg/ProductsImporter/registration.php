<?php

declare(strict_types=1);

/**
 * Extension which allow you to import the product into the Magento Store.
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'DoctorDogg_ProductsImporter',
    __DIR__
);
