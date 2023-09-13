<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Ui\DataProvider\ImportFile\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\ScheduleDesignUpdate;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 *
 * based on: vendor/magento/module-catalog-staging/Ui/DataProvider/Product/Form/Modifier/ScheduleDesignUpdate.php
 */
class MainInfoUpdate implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * Constructor.
     *
     * @param ArrayManager $arrayManager
     * @param ScheduleDesignUpdate $modifier
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        /**
         * @todo: Nothing to do with output meta data.
         */
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /**
         * @todo: Nothing to do here.
         */
        return $data;
    }
}
