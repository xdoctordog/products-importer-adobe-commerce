<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Framework\View\Element\Html\Select;
use \Magento\Framework\View\Element\Context;
use \DoctorDogg\ProductsImporter\Api\RequiredProductAttributesInterface;

/**
 * HTML select element block with (required for creating) Magento core product attributes options.
 */
class ProductAttribute extends Select
{
    /**
     * Required product attributes.
     *
     * @var array|null
     */
    private ?array $requiredProductAttributes = null;

    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Construct
     *
     * @param Context $context
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve required product attributes.
     *
     * @param int|null $productAttributeId return name by customer group id
     * @return array|string
     */
    protected function _getRequiredProductAttributes(?int $productAttributeId = null)
    {
        if ($this->requiredProductAttributes === null) {
            /**
             * @usecase 1
            $this->requiredProductAttributes[RequiredProductAttributesInterface::SKU] = RequiredProductAttributesInterface::SKU;
            $this->requiredProductAttributes[RequiredProductAttributesInterface::PRODUCT_TYPE] = RequiredProductAttributesInterface::PRODUCT_TYPE;
            $this->requiredProductAttributes[RequiredProductAttributesInterface::NAME] = RequiredProductAttributesInterface::NAME;
            $this->requiredProductAttributes[RequiredProductAttributesInterface::PRICE] = RequiredProductAttributesInterface::PRICE;
            $this->requiredProductAttributes[RequiredProductAttributesInterface::URL_KEY] = RequiredProductAttributesInterface::URL_KEY;
            $this->requiredProductAttributes[RequiredProductAttributesInterface::_ATTRIBUTE_SET] = RequiredProductAttributesInterface::_ATTRIBUTE_SET;
             */

            /**
             * @usecase 2
             */
            foreach (RequiredProductAttributesInterface::_ as $requiredAttribute) {
                $this->requiredProductAttributes[$requiredAttribute] = $requiredAttribute;
            }
        }
        if ($productAttributeId !== null) {
            return $this->requiredProductAttributes[$productAttributeId] ?? null;
        }
        return $this->requiredProductAttributes;
    }

    /**
     * Set input name.
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getRequiredProductAttributes() as $productAttributeId => $productAttributeLabel) {
                $this->addOption($productAttributeId, addslashes($productAttributeLabel));
            }
        }
        return parent::_toHtml();
    }
}
