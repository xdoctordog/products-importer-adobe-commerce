<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Exception\LocalizedException;
use \DoctorDogg\ProductsImporter\Api\ProductAttributeAdminSettingInterface;

/**
 * Block that represents the settings which allows to add few available setting-notes using core JS functionality.
 *
 * Class is based on the: \Magento\CatalogInventory\Block\Adminhtml\Form\Field\Minsaleqty
 */
class ProductAdditionalAttributes extends AbstractFieldArray
{
    /**
     * Prepare to render.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_CODE_KEY,
            [
                'label' => __('Product Attribute Code'),
                'class' => 'required-entry admin__control-text'
            ]
        );
        $this->addColumn(
            ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DESCRIPTION,
            [
                'label' => __('Product Attribute Description'),
                'class' => 'required-entry admin__control-text'
            ]
        );
        $this->addColumn(
            ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_POSITION,
            [
                'label' => __('Position of column in import file'),
                'class' => 'required-entry validate-number admin__control-text'
            ]
        );
        $this->addColumn(
            ProductAttributeAdminSettingInterface::PRODUCT_ATTRIBUTE_DEFAULT_VALUE,
            [
                'label' => __('Default value')
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add New Product Field');
    }
}
