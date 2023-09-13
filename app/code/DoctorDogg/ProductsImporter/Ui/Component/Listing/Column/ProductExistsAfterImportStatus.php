<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Ui\Component\Listing\Column;

use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \DoctorDogg\ProductsImporter\Model\Mapper\BufferProductStatusProductExistsAfterImportStatusCodeToText;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;

/**
 * Column values for the field 'product_exists_after_import_status'.
 */
class ProductExistsAfterImportStatus extends Column
{
    /**
     * @var BufferProductStatusProductExistsAfterImportStatusCodeToText
     */
    private BufferProductStatusProductExistsAfterImportStatusCodeToText $mapper;

    /**
     * Constructor.
     *
     * @param BufferProductStatusProductExistsAfterImportStatusCodeToText $mapper
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        BufferProductStatusProductExistsAfterImportStatusCodeToText $mapper,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->mapper = $mapper;
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $extensionAttributes = $item['extension_attributes'] ?? null;
                if (!$extensionAttributes) {
                    $productExistsAfterImportStatus =
                        BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__UNDEFINED;
                } else {
                    $productExistsAfterImportStatus = $extensionAttributes->getProductExistsAfterImportStatus();
                }
                $item['product_exists_after_import_status'] = $this->mapper->map((int)$productExistsAfterImportStatus);
            }
        }

        return $dataSource;
    }
}
