<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml\ImportFile;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreFactory;
use \Magento\Framework\Exception\LocalizedException;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;

/**
 * Builder for the controller: Build an import file based on a request.
 */
class Builder
{
    /**
     * @var ImportFileInterfaceFactory
     */
    protected $importFileInterfaceFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    /**
     * @var ImportFileInterfaceRepositoryInterface
     */
    private $importFileInterfaceRepository;

    /**
     * Constructor
     *
     * @param ImportFileInterfaceFactory $importFileInterfaceFactory
     * @param LoggerInterface $logger
     * @param Registry $registry
     * @param StoreFactory $storeFactory
     * @param ImportFileInterfaceRepositoryInterface $importFileInterfaceRepository
     */
    public function __construct(
        ImportFileInterfaceFactory $importFileInterfaceFactory,
        LoggerInterface $logger,
        Registry $registry,
        StoreFactory $storeFactory,
        ImportFileInterfaceRepositoryInterface $importFileInterfaceRepository
    ) {
        $this->importFileInterfaceFactory = $importFileInterfaceFactory;
        $this->logger = $logger;
        $this->registry = $registry;
        $this->storeFactory = $storeFactory;
        $this->importFileInterfaceRepository = $importFileInterfaceRepository;
    }

    /**
     * Build product based on user request
     *
     * @param RequestInterface $request
     * @return ImportFileInterface
     * @throws \RuntimeException
     * @throws LocalizedException
     */
    public function build(RequestInterface $request): ImportFileInterface
    {
        $importFileId = (int) $request->getParam('id');
        $storeId = $request->getParam('store', 0);

        if ($importFileId) {
            try {
                $importFile = $this->importFileInterfaceRepository->get($importFileId, $storeId);
            } catch (\Exception $e) {
                $importFile = $this->createEmptyImportFile($storeId);
                $this->logger->critical($e);
            }
        } else {
            $importFile = $this->createEmptyImportFile($storeId);
        }

        $this->registry->unregister('import_file');
        $this->registry->unregister('current_import_file');
        $this->registry->register('import_file', $importFile);
        $this->registry->register('current_import_file', $importFile);

        return $importFile;
    }

    /**
     * Create an import file with the given properties.
     *
     * @param int|null $storeId
     * @return ImportFileInterface
     */
    private function createEmptyImportFile($storeId = null): ImportFileInterface
    {
        /** @var $importFile ImportFileInterface */
        $importFile = $this->importFileInterfaceFactory->create();

        return $importFile;
    }
}
