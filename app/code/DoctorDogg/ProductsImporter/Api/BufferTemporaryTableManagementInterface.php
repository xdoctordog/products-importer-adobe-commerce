<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The interface that provides possibility to create the table for the for buffer products:
 *  - Gets the hardcoded required fields
 */

interface BufferTemporaryTableManagementInterface
{
    /**
     * Create temporary table where data for importing products will be stored.
     *
     * @return void
     * @throws \Exception
     * @throws \Zend_Db_Exception
     */
    public function createProductImportTemporaryTable();
}
