<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Ui\Component\Listing\Column;

use \Magento\Ui\Component\Listing\Columns\Column;

/**
 * Column values for the field 'validation_errors'.
 */
class ValidationErrors extends Column
{
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
                $validationErrors = '';
                if ($extensionAttributes) {
                    $validationErrors = $extensionAttributes->getValidationErrors();
                }
                $item['validation_errors'] = $validationErrors;
            }
        }

        return $dataSource;
    }
}
