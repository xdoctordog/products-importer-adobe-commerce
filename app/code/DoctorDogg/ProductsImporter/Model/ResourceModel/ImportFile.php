<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractDb;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;

/**
 * Resource model for import file.
 */
class ImportFile extends AbstractDb
{
    /**
     * @const string TABLE
     */
    public const TABLE = 'doctordogg_productsimporter_importfiles';

    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, ImportFileInterface::ENTITY_ID);
    }
}
