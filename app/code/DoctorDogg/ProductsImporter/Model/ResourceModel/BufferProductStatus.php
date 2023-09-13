<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;

/**
 * Resource model for buffer product status.
 */
class BufferProductStatus extends AbstractDb
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            BufferProductStatusInterface::TABLE,
            BufferProductStatusInterface::ENTITY_ID
        );
    }
}
