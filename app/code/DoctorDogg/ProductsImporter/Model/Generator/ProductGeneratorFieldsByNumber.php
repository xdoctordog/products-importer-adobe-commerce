<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Generator;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\Exception\LocalizedException;
use \DoctorDogg\ProductsImporter\Model\Generator\ProductGeneratorFieldsByNumberInterface;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;

/**
 * Generator of the temporary product's fields by its number.
 */
class ProductGeneratorFieldsByNumber implements ProductGeneratorFieldsByNumberInterface
{
    /**
     * @const string FIELD_NAME_PREFIX
     */
    public const FIELD_NAME_PREFIX = 'dd_field_';

    /**
     * @var ConfigReaderLogger
     */
    private ConfigReaderLogger $configReaderLogger;

    /**
     * Constructor.
     *
     * @param ConfigReaderLogger $configReaderLogger
     */
    public function __construct(
        ConfigReaderLogger $configReaderLogger
    ) {
        $this->configReaderLogger = $configReaderLogger;
    }

    /**
     * Generate fields.
     *
     * @param int $number
     * @return array[]
     * @throws LocalizedException
     */
    public function generate(int $number): array
    {
        $fields = [];
        if ($number <= 0) {
            return $fields;
        }

        $defaultFieldLength = $this->configReaderLogger->getDefaultFieldLengthTempProductTable(__METHOD__);

        if ($defaultFieldLength <= 0) {
            throw new LocalizedException(__('Default field length of temporary product table should not be equal or less than zero'));
        }

        for ($i = 0; $i < $number; $i++) {
            $nameFieldValue = static::FIELD_NAME_PREFIX . (string)$i;
            $fields[] = [
                'name' => $nameFieldValue,
                'type' => Table::TYPE_TEXT,
                'size' => $defaultFieldLength
            ];
        }

        return $fields;
    }
}
