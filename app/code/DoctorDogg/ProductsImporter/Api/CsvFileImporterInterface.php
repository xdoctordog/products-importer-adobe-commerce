<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The interface that imports CSV file into a temporary table for subsequent data import directly into Magento products.
 */
interface CsvFileImporterInterface
{
    /**
     * Import csv file into temporary table.
     *
     * @param string $csvFilePath
     * @return void
     */
    public function import(string $csvFilePath): void;
}
