<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Processor;

use \Magento\Framework\Serialize\SerializerInterface;
use \InvalidArgumentException;
use \DoctorDogg\ProductsImporter\Api\NewProductAttributesProcessorInterface;
use \DoctorDogg\ProductsImporter\Api\ConfigReaderInterface;
use \DoctorDogg\ProductsImporter\Api\ProductAttributesUpdaterInterface;

/**
 * Processor which gets the previous state of the additional attributes of the product and then decide which attributes
 * should be removed from the Product entity and which should be added and adds them.
 */
class NewProductAttributesProcessor implements NewProductAttributesProcessorInterface
{
    /**
     * @var ProductAttributesUpdaterInterface
     */
    private ProductAttributesUpdaterInterface $productAttributesUpdaterInterface;

    /**
     * @var ConfigReaderInterface
     */
    private ConfigReaderInterface $configReaderInterface;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializerInterface;

    /**
     * Constructor.
     *
     * @param ProductAttributesUpdaterInterface $productAttributesUpdaterInterface
     * @param ConfigReaderInterface $configReaderInterface
     * @param SerializerInterface $serializerInterface
     */
    public function __construct(
        ProductAttributesUpdaterInterface $productAttributesUpdaterInterface,
        ConfigReaderInterface $configReaderInterface,
        SerializerInterface $serializerInterface
    ) {
        $this->productAttributesUpdaterInterface = $productAttributesUpdaterInterface;
        $this->configReaderInterface = $configReaderInterface;
        $this->serializerInterface = $serializerInterface;
    }

    /**
     * Process:
     *  - Get previously added product attributes from the config
     *
     * @param array $newCustomProductAttributes
     * @param array $oldArrayProductAttributes
     * @return void
     */
    public function process(array $newCustomProductAttributes, array $oldArrayProductAttributes)
    {
        $previousStateProductAdditionalAttributes = $oldArrayProductAttributes;

        return $this->productAttributesUpdaterInterface
            ->update($newCustomProductAttributes, $previousStateProductAdditionalAttributes);
    }
}
