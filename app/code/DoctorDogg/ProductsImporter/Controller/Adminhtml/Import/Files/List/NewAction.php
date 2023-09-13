<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\Import\Files\List;

use \Magento\Backend\App\Action as BackendAction;
use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\Forward;
use \Magento\Backend\Model\View\Result\ForwardFactory;
use \Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Action for create the new import file.
 */
class NewAction extends BackendAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::import_files_new';

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Create new import file action.
     *
     * @return Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        $resultForward->forward('edit');
        return $resultForward;
    }
}
