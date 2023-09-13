<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field;

use DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field\MagentoCoreProductAttributeInterface;
use \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\DataObject;
use \Magento\Framework\View\Element\BlockInterface;
use \DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field\ProductAttribute;

/**
 * Adminhtml Doctor Dogg "Magento Required Core Product Attributes" field.
 *
 * based on: magento/module-catalog-inventory/Block/Adminhtml/Form/Field/Minsaleqty.php
 */
class MagentoCoreProductAttribute extends AbstractFieldArray
{
    /**
     * @var ProductAttribute|null
     */
    protected ?ProductAttribute $renderer = null;

    /**
     * Retrieve group column renderer.
     *
     * @return ProductAttribute|BlockInterface
     * @throws LocalizedException
     */
    protected function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = $this->getLayout()->createBlock(
                ProductAttribute::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->renderer->setClass('customer_group_select admin__control-select');
        }
        return $this->renderer;
    }

    /**
     * Prepare to render.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE,
            ['label' => __('Required Product Attribute'), 'renderer' => $this->getRenderer()]
        );
        $this->addColumn(
            MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE_POSITION_IMPORT_FILE,
            [
                'label' => __('Position of column in import file'),
                'class' => 'validate-number admin__control-text'
            ]
        );
        $this->addColumn(
            MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE_DEFAULT_VALUE,
            [
                'label' => __('Default value'),
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Required Product Attribute');
    }

    /**
     * Prepare existing row data object.
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getRenderer()->calcOptionHash($row->getData(MagentoCoreProductAttributeInterface::REQUIRED_PRODUCT_ATTRIBUTE))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
