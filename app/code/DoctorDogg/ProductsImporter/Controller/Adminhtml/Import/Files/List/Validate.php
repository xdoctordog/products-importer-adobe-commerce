<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\Import\Files\List;

use \Magento\Backend\App\Action as BackendAction;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\Forward;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Magento\Framework\App\Action\HttpGetActionInterface;
use \Magento\Framework\App\Action\HttpPostActionInterface;
use \Magento\Framework\DataObject;
use \Magento\Framework\Controller\Result\JsonFactory;

/**
 * Action for validation the import file before saving.
 */
class Validate extends BackendAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     * @TODO: need to test this const
     * const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::import_files_validate';
     */

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $response = new DataObject();
        $response->setError(false);
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
