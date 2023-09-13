<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;

/**
 * Resource model for buffer product.
 */
class BufferProduct extends AbstractDb
{
    /**
     * Constructor.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(BufferProductInterface::TABLE, BufferProductInterface::ENTITY_ID);
    }
}
