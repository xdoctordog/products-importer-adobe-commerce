<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\BufferProduct;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Controller for grid with buffer products.
 */
class BufferProductList extends Action implements ActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     * Value is the same as in etc/adminhtml/menu.xml
     *
     * @TODO: update menu.xml file.
     */
    const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::buffer_product_list';

    /**
     * @const string MENU_ITEM_KEY
     */
    const MENU_ITEM_KEY = self::ADMIN_RESOURCE;

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface|ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(static::MENU_ITEM_KEY);
        $this->_view->renderLayout();
    }
}
