<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;
use \DoctorDogg\ProductsImporter\Model\BufferProductStatus;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus as BufferProductStatusResource;

/**
 * Collection class for buffer product statuses.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string $_idFieldName
     */
    protected $_idFieldName = BufferProductStatusInterface::ENTITY_ID;

    /**
     * Initialize resources.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(BufferProductStatus::class, BufferProductStatusResource::class);
    }
}
