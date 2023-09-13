<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as AbstractCollection;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Model\ImportFile;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile as ImportFileResource;

/**
 * Collection class for import file.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string $_idFieldName
     */
    protected $_idFieldName = ImportFileInterface::ENTITY_ID;

    /**
     * Initialize resources.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ImportFile::class, ImportFileResource::class);
    }
}
