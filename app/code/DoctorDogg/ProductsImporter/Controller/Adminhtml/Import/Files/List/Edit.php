<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\Import\Files\List;

use \Magento\Backend\App\Action\Context;
use \Magento\Backend\Model\View\Result\Page;
use \Magento\Backend\Model\View\Result\Redirect;
use \Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use \Magento\Framework\Controller\ResultInterface;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \DoctorDogg\ProductsImporter\Controller\Adminhtml\ImportFileAbstractBackendAction;
use \DoctorDogg\ProductsImporter\Controller\Adminhtml\ImportFile\Builder as ImportFileBuilder;

/**
 * Edit action for the admin page with form for editing the import file.
 */
class Edit extends ImportFileAbstractBackendAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::import_files_edit';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = ['edit'];

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param ImportFileBuilder $importFileBuilder
     * @param PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ImportFileBuilder $importFileBuilder,
        PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $importFileBuilder);
        $this->resultPageFactory = $resultPageFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Import file edit form.
     *
     * @return ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $store = $this->storeManager->getStore($storeId);
        $this->storeManager->setCurrentStore($store->getCode());
        $importFileId = (int) $this->getRequest()->getParam('id');
        $importFile = $this->importFileBuilder->build($this->getRequest());

        if ($importFileId && !$importFile->getEntityId()) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('This import file doesn\'t exist.'));
            return $resultRedirect->setPath('doctordogg_productsimporter/import_files_list/index');
        }

        $this->_eventManager->dispatch('doctordogg_productsimporter_import_file_edit_action', ['import_file' => $importFile]);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /**
         * @todo: Add type ID for the import file if needed.
         */
        $resultPage->addHandle('doctordogg_productsimporter_import_file_' . 'simple' /* $importFile->getTypeId()*/);

        $resultPage->setActiveMenu('DoctorDogg_ProductsImporter::import_files_list');

        if ($importFileId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Import File'));
            $resultPage->getConfig()->getTitle()->prepend($importFile->getName());
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Import File'));
        }

        if (!$this->storeManager->isSingleStoreMode()
            && ($switchBlock = $resultPage->getLayout()->getBlock('store_switcher'))
        ) {
            $switchBlock->setDefaultStoreName(__('Default Values'))
                ->setWebsiteIds($importFile->getWebsiteIds())
                ->setSwitchUrl(
                    $this->getUrl(
                        'doctordogg_productsimporter/*/*',
                        ['_current' => true, 'active_tab' => null, 'tab' => null, 'store' => null]
                    )
                );
        }

        return $resultPage;
    }
}
