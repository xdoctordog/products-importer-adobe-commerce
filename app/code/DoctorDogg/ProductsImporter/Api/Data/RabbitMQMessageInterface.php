<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api\Data;

/**
 * Universal interface for message for the RabbitMQ queue.
 * Because we do not know in advance which fields will be in the product in advance.
 */
interface RabbitMQMessageInterface
{
    /**
     * Get universal data.
     *
     * @return string
     */
    public function getUniversalData(): string;

    /**
     * Set universal data.
     *
     * @param string $universalData
     * @return void
     */
    public function setUniversalData(string $universalData): void;
}
