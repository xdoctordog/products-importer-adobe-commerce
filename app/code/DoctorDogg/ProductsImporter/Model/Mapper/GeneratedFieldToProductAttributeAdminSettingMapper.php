<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Mapper;

use \DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field\MagentoCoreProductAttributeInterface;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;
use \DoctorDogg\ProductsImporter\Model\Generator\ProductGeneratorFieldsByNumber;
use \DoctorDogg\ProductsImporter\Model\Mapper\GeneratedFieldToProductAttributeAdminSettingMapperInterface;
use \DoctorDogg\ProductsImporter\Api\ProductAttributeAdminSettingInterface;

/**
 * Mapper which is using the auto generated field names of the temporary table with buffer products
 * and map them to the Admin Settings of the required fields and custom product fields.
 */
class GeneratedFieldToProductAttributeAdminSettingMapper implements GeneratedFieldToProductAttributeAdminSettingMapperInterface
{
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
     * Map fields and return prepared array with data.
     *
     * @return string[]
     */
    public function map(array $inputData): array
    {
        $productRequiredCoreAttributes = $this->configReaderLogger->getProductRequiredCoreAttributes(__METHOD__);
        $productAdditionalAttributes = $this->configReaderLogger->getProductAdditionalAttributes(__METHOD__);

        $outputData = [];
        $outputData = $this->mapArray($productRequiredCoreAttributes, $inputData, $outputData, $isRequiredAttr = true);
        $outputData = $this->mapArray($productAdditionalAttributes, $inputData, $outputData, $isRequiredAttr = false);

        return $outputData;
    }

    /**
     * Map fields which are in admin settings with the data come from the buffer table / import file.
     * Required core Magento attributes of the product.
     *
     * @param array $productAttributes
     * @param array $inputData
     * @param array $outputData
     * @param bool $isRequiredAttr
     * @return array
     */
    private function mapArray(array $productAttributes, array $inputData, array $outputData = [], bool $isRequiredAttr = true): array
    {
        if ($isRequiredAttr) {
            $attributeNameKey = MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE;
            $positionKey = MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE_POSITION_IMPORT_FILE;
            $defaultValueKey = MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE_DEFAULT_VALUE;
            $attributePrefix = '';
        } else {
            /**
             * Case for processing the additional product attributes.
             */

            $attributeNameKey = ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_CODE_KEY;
            $positionKey = ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_POSITION;
            $defaultValueKey = ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DEFAULT_VALUE;
            $attributePrefix = ProductAttributeAdminSettingInterface::EXTENSION_PREFIX;
        }

        foreach ($productAttributes as $attribute) {
            $attributeName = $attribute[$attributeNameKey] ?? null;
            $position = $attribute[$positionKey] ?? null;
            if ($attributeName === null
                || $position === null
            ) {
                continue;
            }
            $defaultValue = $attribute[$defaultValueKey] ?? null;
            $position = (string)(int)$position;
            $value = $inputData[ProductGeneratorFieldsByNumber::FIELD_NAME_PREFIX . $position] ?? null;

            $outputData[$attributePrefix . $attributeName] = $value ?? $defaultValue;
        }

        return $outputData;
    }
}
