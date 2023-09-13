<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api\Data;

/**
 * The interface contains the constant for topic which is used to publish messages for import buffer products.
 */
interface RabbitMQTopicInterface
{
    /**
     * @const string
     */
    public const TOPIC = 'doctordogg_productsimporter_topic';
}
