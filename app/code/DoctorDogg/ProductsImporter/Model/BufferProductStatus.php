<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Framework\Api\AttributeValueFactory;
use \Magento\Framework\Api\ExtensionAttributesFactory;
use \Magento\Framework\Data\Collection\AbstractDb;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Model\AbstractExtensibleModel;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Registry;
use \Magento\Framework\Serialize\Serializer\Json;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusExtensionInterface;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProductStatus as BufferProductStatusResource;

/**
 * Main model for the buffer product status entity.
 */
class BufferProductStatus extends AbstractExtensibleModel implements BufferProductStatusInterface
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'doctordogg_productsimporter_buffer_product_status';

    /**
     * @var Json
     */
    private Json $json;

    /**
     * Constructor.
     *
     * @param Json $json
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param AbstractResource $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Json $json,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->json = $json;
    }

    /**
     * Make relation with resource table.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(BufferProductStatusResource::class);
    }

    /**
     * Get entity id.
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return (int) $this->_getData(BufferProductStatusInterface::ENTITY_ID);
    }

    /**
     * Set entity id.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self
    {
        $this->setData(BufferProductStatusInterface::ENTITY_ID, $entityId);
        return $this;
    }

    /**
     * Get buffer product status id.
     *
     * @return int
     */
    public function getBufferProductId(): int
    {
        return (int) $this->_getData(BufferProductStatusInterface::BUFFER_PRODUCT_ID);
    }

    /**
     * Set buffer product status id.
     *
     * @param int $bufferProductId
     * @return $this
     */
    public function setBufferProductId($bufferProductId): self
    {
        $this->setData(BufferProductStatusInterface::BUFFER_PRODUCT_ID, $bufferProductId);
        return $this;
    }

    /**
     * Get status if the product is checked after importing process.
     *
     * @return int
     * @throws LocalizedException
     */
    public function getProductExistsAfterImportStatus(): int
    {
        $productExistsAfterImportStatus = (int) $this->_getData(BufferProductStatusInterface::PRODUCT_EXISTS_AFTER_IMPORT_STATUS_KEY);

        if (!\in_array($productExistsAfterImportStatus, BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT)) {
            throw new LocalizedException(__(
                'Incorrect value from DB for the status of checking if the product exists after importing.'
            ));
        }

        return $productExistsAfterImportStatus;
    }

    /**
     * Set status if the product is checked after importing process.
     *
     * @param int $productExistsAfterImportStatus
     * @return $this
     * @throws LocalizedException
     */
    public function setProductExistsAfterImportStatus(int $productExistsAfterImportStatus): self
    {
        if (!\in_array($productExistsAfterImportStatus, BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT)) {
            throw new LocalizedException(__(
                'Incorrect value for the status of checking if the product exists after importing.'
            ));
        }
        $this->setData(BufferProductStatusInterface::PRODUCT_EXISTS_AFTER_IMPORT_STATUS_KEY, $productExistsAfterImportStatus);
        return $this;
    }

    /**
     * Get validation errors.
     *
     * @return array|string[]
     */
    public function getValidationErrors(): array
    {
        $validationErrorsString = $this->_getData(BufferProductStatusInterface::VALIDATION_ERRORS_KEY);

        try {
            $validationErrors = $this->json->unserialize($validationErrorsString);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            $validationErrors = [];
        }

        return (array)$validationErrors;
    }

    /**
     * Set validation errors.
     *
     * @param string[] $validationErrors
     * @return $this
     */
    public function setValidationErrors(array $validationErrors): self
    {
        try {
            $validationErrorsString = $this->json->serialize($validationErrors);
        } catch (\InvalidArgumentException $invalidArgumentException) {
        }

        $this->setData(BufferProductStatusInterface::VALIDATION_ERRORS_KEY, $validationErrorsString);

        return $this;
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusExtensionInterface|null
     */
    public function getExtensionAttributes(): BufferProductStatusExtensionInterface
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param BufferProductStatusExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(BufferProductStatusExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
