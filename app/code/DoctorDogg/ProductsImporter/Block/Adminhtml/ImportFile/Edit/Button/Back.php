<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Block\Adminhtml\ImportFile\Edit\Button;

use \DoctorDogg\ProductsImporter\Block\Adminhtml\ImportFile\Edit\Button\Generic;

/**
 * Button Back for the page with editing Import File.
 */
class Back extends Generic
{
    /**
     * Get Button Data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back
     *
     * @return string
     */
    private function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
