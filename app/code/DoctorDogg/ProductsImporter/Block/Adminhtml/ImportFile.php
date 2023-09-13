<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml;

use \Magento\Backend\Block\Widget\Button\SplitButton;
use \Magento\Backend\Block\Widget\Container;

/**
 * Button new import file.
 *
 * When we need some modifications we can use this base class.
 * based on class: vendor/magento/module-catalog/Block/Adminhtml/Product.php
 */
class ImportFile extends Container
{
    /**
     * Prepare button and grid
     *
     * @return ImportFile
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_import_file',
            'label' => __('Add Import File'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => SplitButton::class,
            'options' => [],
            'dropdown_button_aria_label' => __('Add import file of type'),
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }
}
