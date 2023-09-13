<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Helper;

use \Magento\Framework\Serialize\Serializer\Json;
use \Psr\Log\LoggerInterface;
use \InvalidArgumentException;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * Helper for converting the array of config values to json string for saving in DB
 * and back from json string to array on loading the config values for displaying them in admin panel.
 *
 * Analogue: vendor/magento/module-catalog-inventory/Helper/Minsaleqty.php
 */
class ProductAdditionalAttributes
{
    /**
     * Constructor.
     *
     * @param Json $json
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        private Json $json,
        private LogMessagePreparerInterface $logMessagePreparerInterface,
        private LoggerInterface $loggerInterface
    ) {
    }

    /**
     * Unserialize values of config before displaying on the admin panel.
     *
     * @param $stringConfigValue
     * @return mixed
     */
    public function unserialize($stringConfigValue)
    {
        $response = null;
        try {
            $response = $this->json->unserialize($stringConfigValue);
            unset($response['__empty']);//@TODO: Not sure if we can just unset this value.
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->loggerInterface->info(
                $this->logMessagePreparerInterface->getErrorMessage($invalidArgumentException)
            );
        }

        return $response;
    }

    /**
     * Serialize values of config before saving.
     *
     * @param array $value
     * @return false|string
     */
    public function serialize(array $value)
    {
        foreach ($value as $key => $item) {
            if ($key === '__empty') {
                unset($value[$key]);//@TODO: Not sure if we can just unset this value.
            }
        }

        return $this->json->serialize($value);
    }
}
