<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Config\Reader;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\Serialize\Serializer\Json;
use \Magento\Store\Model\ScopeInterface;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\ProductsImporter\Api\ConfigReaderInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * Reader of config values.
 */
class ConfigReader implements ConfigReaderInterface
{
    /**
     * @const string PATH_PREVIOUS_PRODUCT_ADDITONAL_ATTRIBUTES
     */
    public const PATH_PREVIOUS_PRODUCT_ADDITONAL_ATTRIBUTES =
        'doctordogg_productsimporter_settings/additional_product_attributes_group/previous_product_additional_attributes';

    /**
     * @const string PATH_PRODUCT_ADDITONAL_ATTRIBUTES
     */
    public const PATH_PRODUCT_ADDITONAL_ATTRIBUTES =
        'doctordogg_productsimporter_settings/additional_product_attributes_group/product_additional_attributes';

    /**
     * @const string PATH_PRODUCT_REQUIRED_CORE_ATTRIBUTES
     */
    public const PATH_PRODUCT_REQUIRED_CORE_ATTRIBUTES =
        'doctordogg_productsimporter_settings/magento_core_product_attributes_group/product_required_core_attributes';

    /**
     * @const string PATH_REMOVE_PREVIOUSLY_ADDED_CUSTOM_ATTRIBUTES
     */
    public const PATH_REMOVE_PREVIOUSLY_ADDED_CUSTOM_ATTRIBUTES =
        'doctordogg_productsimporter_settings/additional_product_attributes_group/remove_previously_added_custom_attributes';

    /**
     * @const string PATH_IMPORT_KNIFE_SWITCHER
     */
    public const PATH_IMPORT_KNIFE_SWITCHER =
        'doctordogg_productsimporter_settings/general_setting_group/import_knife_switcher';

    /**
     * @const string PATH_NUMBER_PRODUCTS_SCHEDULED_AT_TIME
     */
    public const PATH_NUMBER_PRODUCTS_SCHEDULED_AT_TIME =
        'doctordogg_productsimporter_settings/general_setting_group/number_products_to_be_scheduled_at_time';

    /**
     * @const string PATH_CHECK_PRODUCT_EXISTS_AFTER_IMPORT
     */
    public const PATH_CHECK_PRODUCT_EXISTS_AFTER_IMPORT =
        'doctordogg_productsimporter_settings/general_setting_group/check_product_exists_after_import';

    /**
     * @const string PATH_NUMBER_COLUMNS_IN_TEMPORARY_BUFFER_PRODUCT_TABLE
     */
    public const PATH_NUMBER_COLUMNS_IN_TEMPORARY_BUFFER_PRODUCT_TABLE =
        'doctordogg_productsimporter_settings/general_setting_group/number_columns_in_temporary_buffer_product_table';

    /**
     * @const string PATH_DEFAULT_FIELD_LENGTH_TEMP_PRD_TBL
     */
    public const PATH_DEFAULT_FIELD_LENGTH_TEMP_PRD_TBL =
        'doctordogg_productsimporter_settings/general_setting_group/default_field_length';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfigInterface;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param Json $json
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        Json $json,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparerInterface
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->json = $json;
        $this->logger = $logger;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
    }

    /**
     * Get previous state product additional attributes.
     * @deprecated
     *
     * @return string|null
     */
    public function getPreviousStateProductAdditionalAttributes(): ?string
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_PREVIOUS_PRODUCT_ADDITONAL_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE
        );
        return ($value) ? (string)$value : null;
    }

    /**
     * Get required core Magento product's attributes.
     *
     * @return array
     */
    public function getProductRequiredCoreAttributes(): array
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_PRODUCT_REQUIRED_CORE_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE
        );

        $arrData = [];
        try {
            $arrData = $this->json->unserialize(\is_string($value) ? $value : '');
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($invalidArgumentException));
        }

        return \is_array($arrData) ? $arrData : [];
    }

    /**
     * Get product's additional attributes.
     *
     * @return array
     */
    public function getProductAdditionalAttributes(): array
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_PRODUCT_ADDITONAL_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE
        );

        $arrData = [];
        try {
            $arrData = $this->json->unserialize(\is_string($value) ? $value : '');
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($invalidArgumentException));
        }

        return \is_array($arrData) ? $arrData : [];
    }

    /**
     * Get status about if we are going to remove previously added custom attributes.
     *
     * @return bool|null
     */
    public function getRemovePreviouslyAddedCustomAttributes(): ?bool
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_REMOVE_PREVIOUSLY_ADDED_CUSTOM_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE
        );

        return $this->_getNullBoolean($value);
    }

    /**
     * Get knife switcher.
     *
     * @return bool|null
     */
    public function getImportKnifeSwitcher(): ?bool
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_IMPORT_KNIFE_SWITCHER,
            ScopeInterface::SCOPE_STORE
        );

        return $this->_getNullBoolean($value);
    }

    /**
     * Get number products scheduled at time for importing.
     *
     * @return int|null
     */
    public function getNumberProductsScheduledAtTime(): ?int
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_NUMBER_PRODUCTS_SCHEDULED_AT_TIME,
            ScopeInterface::SCOPE_STORE
        );

        return ($value) ? (int)$value : null;
    }

    /**
     * Get if we should check if the imported product really exists.
     *
     * @return bool|null
     */
    public function getCheckProductExistsAfterImport(): ?bool
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_CHECK_PRODUCT_EXISTS_AFTER_IMPORT,
            ScopeInterface::SCOPE_STORE
        );

        return $this->_getNullBoolean($value);
    }

    /**
     * Get number of columns which will be added to the temporary table for the buffer product.
     *
     * @return int|null
     */
    public function getNumberColumnsInTemporaryBufferTable(): ?int
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_NUMBER_COLUMNS_IN_TEMPORARY_BUFFER_PRODUCT_TABLE,
            ScopeInterface::SCOPE_STORE
        );

        return ($value) ? (int)$value : null;
    }

    /**
     * Get default field length of the temporary table with the buffer products.
     *
     * @return int|null
     */
    public function getDefaultFieldLengthTempProductTable(): ?int
    {
        $value = $this->scopeConfigInterface->getValue(
            self::PATH_DEFAULT_FIELD_LENGTH_TEMP_PRD_TBL,
            ScopeInterface::SCOPE_STORE
        );

        return ($value) ? (int)$value : null;
    }

    /**
     * Get boolean value.
     *
     * @param $value
     * @return bool|null
     */
    private function _getNullBoolean($value)
    {
        if ($value !== '0' && $value !== 0 && $value !== '1' && $value !== 1) {
            $value = null;
        } else {
            $value = (bool)(int)$value;
        }

        return $value;
    }
}
