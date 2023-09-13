<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\System\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use \InvalidArgumentException;
use DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use DoctorDogg\ProductsImporter\Helper\ProductAdditionalAttributes as ProductAdditionalAttributesHelper;

/**
 * Class which prepares the input data for Magento 2 Core Product Attributes from admin frontend for saving it in DB,
 * and prepares the data from the DB for outputting them on the admin frontend side.
 *
 * Class is based on the: \Magento\CatalogInventory\Model\System\Config\Backend\Minsaleqty
 */
class ProductCoreAttributes extends ConfigValue
{
    /**
     * @var ProductAdditionalAttributesHelper
     */
    private ProductAdditionalAttributesHelper $productAdditionalAttributesHelper;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $loggerInterface;

    /**
     * Constructor.
     *
     * @param ProductAdditionalAttributesHelper $productAdditionalAttributesHelper
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
        ProductAdditionalAttributesHelper $productAdditionalAttributesHelper,
        LogMessagePreparerInterface $logMessagePreparerInterface,
        LoggerInterface $loggerInterface,
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);

        $this->productAdditionalAttributesHelper = $productAdditionalAttributesHelper;

        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
        $this->loggerInterface = $loggerInterface;
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
        $value = $this->productAdditionalAttributesHelper->serialize($newArrayProductAttributes);
        $this->setValue($value);
    }
}
