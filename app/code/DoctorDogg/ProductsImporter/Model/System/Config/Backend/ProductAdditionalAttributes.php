<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\System\Config\Backend;

use DoctorDogg\ProductsImporter\Helper\ProductAdditionalAttributes as ProductAdditionalAttributesHelper;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use \InvalidArgumentException;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\ProductsImporter\Api\NewProductAttributesProcessorInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Model\Guarantee\ProductAdditionalAttributesGuarantee;

/**
 * Class which prepares the input data for Additional Product Attributes from admin frontend for saving it in DB,
 * and prepares the data from the DB for outputting them on the admin frontend side.
 *
 * Class is based on the: \Magento\CatalogInventory\Model\System\Config\Backend\Minsaleqty
 */
class ProductAdditionalAttributes extends ConfigValue
{
    /**
     * @var ProductAdditionalAttributesGuarantee
     */
    private ProductAdditionalAttributesGuarantee $productAdditionalAttributesGuarantee;

    /**
     * Constructor.
     *
     * @param NewProductAttributesProcessorInterface $newProductAttributesProcessorInterface
     * @param ProductAdditionalAttributesHelper $productAdditionalAttributesHelper
     * @param Json $json
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     * @param LoggerInterface $loggerInterface
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        private NewProductAttributesProcessorInterface $newProductAttributesProcessorInterface,
        private ProductAdditionalAttributesHelper $productAdditionalAttributesHelper,
        private Json $json,
        private LogMessagePreparerInterface $logMessagePreparerInterface,
        private LoggerInterface $loggerInterface,
        ProductAdditionalAttributesGuarantee $productAdditionalAttributesGuarantee,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->productAdditionalAttributesGuarantee = $productAdditionalAttributesGuarantee;
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->productAdditionalAttributesHelper->unserialize($value);
        if (\is_array($value)) {
            $value = $this->productAdditionalAttributesGuarantee->guarantee($value);
        }
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $newArrayProductAttributes = \is_array($value) ? $value : [];
        $oldArrayProductAttributes = $this->getOldValueArray();
        $value = $this->productAdditionalAttributesHelper->serialize($newArrayProductAttributes);
        $this->setValue($value);

        $this->newProductAttributesProcessorInterface->process($newArrayProductAttributes, $oldArrayProductAttributes);
    }

    /**
     * Get old value in array representation.
     *
     * @return array
     */
    public function getOldValueArray(): array
    {
        $oldValue = [];
        try {
            $oldValue = $this->json->unserialize($this->getOldValue());
            if (!\is_array($oldValue)) {
                $oldValue = [];
            }
        } catch (InvalidArgumentException $invalidArgumentException) {
            $this->loggerInterface->info(
                $this->logMessagePreparerInterface->getErrorMessage($invalidArgumentException)
            );
        }

        return $oldValue;
    }
}
