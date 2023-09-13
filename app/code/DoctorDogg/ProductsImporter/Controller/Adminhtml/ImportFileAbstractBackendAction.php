<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Controller\Adminhtml;

use \Magento\Backend\App\Action;
use \Magento\Backend\App\Action\Context;
use \DoctorDogg\ProductsImporter\Controller\Adminhtml\ImportFile\Builder as ImportFileBuilder;

/**
 * Abstract base class for controller to control the import file entities.
 */
abstract class ImportFileAbstractBackendAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'DoctorDogg_ProductsImporter::import_files_abstract';

    /**
     * @var ImportFileBuilder
     */
    protected $importFileBuilder;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param ImportFileBuilder $importFileBuilder
     */
    public function __construct(
        Context $context,
        ImportFileBuilder $importFileBuilder
    ) {
        $this->importFileBuilder = $importFileBuilder;
        parent::__construct($context);
    }
}
