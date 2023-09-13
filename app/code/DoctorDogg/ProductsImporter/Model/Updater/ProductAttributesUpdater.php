<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Updater;

use \Magento\Framework\App\Cache\Frontend\Pool;
use \Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use \Magento\Framework\App\RequestInterface;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Api\ConfigReaderInterface;
use \DoctorDogg\ProductsImporter\Api\ProductAttributesUpdaterInterface;
use \DoctorDogg\ProductsImporter\Api\ProductAttributeAdminSettingInterface;
use \DoctorDogg\ProductsImporter\Model\ProductAttributeManager;

/**
 * {@inheritdoc}
 */
class ProductAttributesUpdater implements ProductAttributesUpdaterInterface
{
    /**
     * @var ProductAttributeManager
     */
    private ProductAttributeManager $productAttributeManager;

    /**
     * @var ConfigReaderInterface
     */
    private ConfigReaderInterface $configReaderInterface;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var Pool
     */
    private Pool $pool;

    /**
     * @var CacheTypeListInterface
     */
    private CacheTypeListInterface $cacheTypeListInterface;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $loggerInterface;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * Constructor.
     *
     * @param ProductAttributeManager $productAttributeManager
     * @param ConfigReaderInterface $configReaderInterface
     * @param RequestInterface $request
     * @param Pool $pool
     * @param CacheTypeListInterface $cacheTypeListInterface
     * @param LoggerInterface $loggerInterface
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     */
    public function __construct(
        ProductAttributeManager $productAttributeManager,
        ConfigReaderInterface $configReaderInterface,
        RequestInterface $request,
        Pool $pool,
        CacheTypeListInterface $cacheTypeListInterface,
        LoggerInterface $loggerInterface,
        LogMessagePreparerInterface $logMessagePreparerInterface
    ) {
        $this->productAttributeManager = $productAttributeManager;
        $this->configReaderInterface = $configReaderInterface;
        $this->request = $request;
        $this->pool = $pool;
        $this->cacheTypeListInterface = $cacheTypeListInterface;
        $this->loggerInterface = $loggerInterface;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function update(?array $newCustomProductAttributes, ?array $previouslyAddedCustomProductAttributes): bool
    {
        $somethingAdded = false;
        $somethingRemoved = false;
        $preparedNewCustomProductAttributes =
            $this->prepareCustomProductAttributes(
                ($newCustomProductAttributes) ? $newCustomProductAttributes : []
            );
        $preparedPreviouslyAddedCustomProductAttributes =
            $this->prepareCustomProductAttributes(
                ($previouslyAddedCustomProductAttributes) ? $previouslyAddedCustomProductAttributes : []
            );
        $attributesToAdd = \array_diff_key(
            $preparedNewCustomProductAttributes,
            $preparedPreviouslyAddedCustomProductAttributes
        );
        foreach ($attributesToAdd as $attributeCode => $attribute) {
            $label = $attribute[ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DESCRIPTION_KEY] ?? null;
            if (!$label && !$attributeCode) {
                continue;
            }

            $this->productAttributeManager->add(
                ProductAttributeAdminSettingInterface::EXTENSION_PREFIX . $attributeCode,
                $label
            );
            $somethingAdded = true;
        }

        $shouldRemove = $this->getRemovePreviouslyAddedCustomAttributes();
        if ($shouldRemove) {
            $attributesToRemove = \array_diff_key(
                $preparedPreviouslyAddedCustomProductAttributes,
                $preparedNewCustomProductAttributes
            );
            foreach ($attributesToRemove as $attributeCode => $attribute) {
                $this->productAttributeManager->delete(ProductAttributeAdminSettingInterface::EXTENSION_PREFIX . $attributeCode);
                $somethingRemoved = true;
            }
        }

        if ($somethingAdded || $somethingRemoved) {
            /**
             * Clean cache because saving values doesn't reflect on the frontend of the admin area.
             */
            $this->cleanCache();
        }

        return true;
    }

    /**
     * Clean cache.
     *
     * @return void
     */
    public function cleanCache(): void
    {
        try {
            $types = [
                'eav',
            ];
            foreach ($types as $type) {
                $this->cacheTypeListInterface->cleanType($type);
            }

            foreach ($this->pool as $cacheFrontend) {
                $cacheFrontend->getBackend()->clean();
            }
        } catch (\Throwable $throwable) {
            $this->loggerInterface->alert($this->logMessagePreparerInterface->getErrorMessage($throwable));
        }
    }

    /**
     * Check if we should remove previously added attributes.
     *
     * @return bool
     */
    public function getRemovePreviouslyAddedCustomAttributes(): bool
    {
        $parameters = $this->request->getParams();
        $shouldRemove = $parameters['groups']['additional_product_attributes_group']['fields']['remove_previously_added_custom_attributes']['value'] ?? null;
        if ($shouldRemove === null) {
            $shouldRemove = $this->configReaderInterface->getRemovePreviouslyAddedCustomAttributes();
            $shouldRemove = ($shouldRemove === null) ? false : $shouldRemove;
        } else {
            $shouldRemove = (bool)(int)$shouldRemove;
        }

        return $shouldRemove;
    }

    /**
     * Prepare product attributes to work with them later.
     *
     * @depends On input data.
     *
     * @param array $customProductAttributes
     * @return array
     */
    private function prepareCustomProductAttributes(array $customProductAttributes)
    {
        $preparedCustomProductAttributes = [];

        foreach ($customProductAttributes as $attribute) {
            $key = $attribute[ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_CODE_KEY] ?? null;
            if (!$key) {
                continue;
            }
            $preparedCustomProductAttributes[$key] = $attribute;
        }

        return $preparedCustomProductAttributes;
    }
}
