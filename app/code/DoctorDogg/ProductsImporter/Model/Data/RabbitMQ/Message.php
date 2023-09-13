<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Data\RabbitMQ;

use Magento\Framework\DataObject;

use DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterface;

/**
 * Universal message for the RabbitMQ queue.
 * Because we do not know in advance which fields will be in the product in advance.
 */
class Message extends DataObject implements RabbitMQMessageInterface
{
    /**
     * Get universal data.
     *
     * @return string
     */
    public function getUniversalData(): string
    {
        return (string)$this->getData('universalData');
    }

    /**
     * Set universal data.
     *
     * @param string $universalData
     * @return void
     */
    public function setUniversalData(string $universalData): void
    {
        $this->setData('universalData', $universalData);
    }
}
