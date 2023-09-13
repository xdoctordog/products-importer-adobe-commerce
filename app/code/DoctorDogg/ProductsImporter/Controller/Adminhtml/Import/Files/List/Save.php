<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\Import\Files\List;

use \Magento\Backend\App\Action as BackendAction;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\App\Action\HttpPostActionInterface;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;

/**
 * Action for save the import file.
 */
class Save extends BackendAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     * @TODO: need to test this const
     * const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::import_files_save';
     */

    /**
     * @var ImportFileInterfaceFactory
     */
    private ImportFileInterfaceFactory $importFileInterfaceFactory;

    /**
     * @var ImportFileInterfaceRepositoryInterface
     */
    private ImportFileInterfaceRepositoryInterface $importFileInterfaceRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparer;

    public function __construct(
        Context $context,
        ImportFileInterfaceFactory $importFileInterfaceFactory,
        ImportFileInterfaceRepositoryInterface $importFileInterfaceRepository,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparer
    ) {
        parent::__construct($context);
        $this->importFileInterfaceFactory = $importFileInterfaceFactory;
        $this->importFileInterfaceRepository = $importFileInterfaceRepository;
        $this->logger = $logger;
        $this->logMessagePreparer = $logMessagePreparer;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $importFileId = $this->getRequest()->getPost('entity_id');

        if ($this->getRequest()->getPostValue()) {
            try {
                if ($importFileId) {
                    $importFile = $this->importFileInterfaceRepository->get((int)$importFileId);
                } else {
                    $importFile = $this->importFileInterfaceFactory->create();
                }
                $importFile->setFilePath($this->getRequest()->getPost(ImportFileInterface::FILE_PATH) ?? '');
                $importFile->setIsProcessed((bool)($this->getRequest()->getPost(ImportFileInterface::IS_PROCESSED) ?? false));
                $this->importFileInterfaceRepository->save($importFile);
            } catch (\Throwable $throwable) {
                $this->logger->info($this->logMessagePreparer->getErrorMessage($throwable));
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        /**
         * @TODO: When it is needed, let's add some checking.
         */
        $returnToEdit = false;

        if ($returnToEdit) {
            if ($importFileId) {
                $resultRedirect->setPath(
                    'doctordogg_productsimporter/*/edit',
                    ['id' => $importFileId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'doctordogg_productsimporter/*/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('doctordogg_productsimporter/import_files_list/index');
        }
        return $resultRedirect;
    }

    /**
     * Retrieve current import file ID.
     *
     * @return int
     */
    private function getCurrentImportFileId()
    {
        $originalRequestData = $this->getRequest()->getPostValue('import_file');
        $importFileId = $originalRequestData['entity_id'] ?? null;

        return $importFileId;
    }
}
