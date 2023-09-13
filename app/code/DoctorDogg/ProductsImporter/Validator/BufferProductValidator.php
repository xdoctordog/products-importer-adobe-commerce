<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Validator;

use \Magento\Framework\Exception\LocalizedException;
use \Magento\ImportExport\Model\Import;
use \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Model\Magento\ImportExport\Model\Import\Source\Csv\FictionalCsv;
use \DoctorDogg\ProductsImporter\Model\Magento\ImportExport\Model\Import\Source\Csv\FictionalCsvFactory;
use \DoctorDogg\ProductsImporter\Validator\BufferProductValidatorInterface;

/**
 * Class which validates the product before importing / adding.
 *
 * This is hack for using original Magento 2 Core validation of the product.
 */
class BufferProductValidator implements BufferProductValidatorInterface
{
    /**
     * @var Import
     */
    private Import $import;

    /**
     * @var FictionalCsvFactory
     */
    private FictionalCsvFactory $fictionalCsvFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * Constructor.
     *
     * @param Import $import
     * @param FictionalCsvFactory $fictionalCsvFactory
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     */
    public function __construct(
        Import $import,
        FictionalCsvFactory $fictionalCsvFactory,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparerInterface
    ) {
        $this->import = $import;
        $this->fictionalCsvFactory = $fictionalCsvFactory;
        $this->logger = $logger;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
    }

    /**
     * Check if product is valid for importing.
     *
     * @param array $productData
     * @return bool
     * @throws LocalizedException
     */
    public function validateProduct(array $productData): bool
    {
        $columnNames = \array_keys($productData);
        $productInfo = \array_values($productData);

        /**
         * @var FictionalCsv $fictionalCsvSource
         */
        $fictionalCsvSource = $this->fictionalCsvFactory->create(
            [
                'columnNames' => $columnNames,
            ]
        );
        $fictionalCsvSource->addProductInfo($productInfo);

        $this->import->setData([
            'form_key' => '',
            'entity' => 'catalog_product',
            'behavior' => 'append',
            'validation_strategy' => 'validation-skip-errors',
            'allowed_error_count' => '10',
            '_import_field_separator' => ',',
            '_import_multiple_value_separator' => ',',
            '_import_empty_attribute_value_constant' => '__EMPTY__VALUE__',
            'import_images_file_dir' => '',
            '_import_ids' => '',
        ]);

        /**
         * Also we can return this $result from this method.
         */
        $result = $this->import->validateSource($fictionalCsvSource);

        $errorAggregator = $this->import->getErrorAggregator();
        $errorsCount = $errorAggregator->getErrorsCount();

        if ($errorsCount > 0) {
            $this->logErrors($productData['sku'] ?? '<undefined>', $errorAggregator->getAllErrors());
        }

        return $errorsCount === 0;
    }

    /**
     * Log errors.
     *
     * @param string $sku
     * @param ProcessingError[] $allErrors
     * @return void
     */
    private function logErrors(string $sku, array $allErrors)
    {
        foreach ($allErrors as $error) {
            $errorMessage = $error->getErrorMessage();
            $this->logger->info(
                PHP_EOL .
                '[DoctorDogg_ProductsImporter]: ' . (string)\date(DATE_RFC2822) . PHP_EOL
                . 'Buffer product is not valid with SKU: '
                . $sku . PHP_EOL
                . 'Error: '
                . $errorMessage . PHP_EOL
            );
        }
    }

    /**
     * Get error aggregator.
     *
     * @return ProcessingErrorAggregatorInterface|null
     */
    public function getErrorAggregator(): ?ProcessingErrorAggregatorInterface
    {
        try {
            $errorAggregator = $this->import->getErrorAggregator();
        } catch (\Throwable $throwable) {
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));
            $errorAggregator = null;
        }

        return $errorAggregator;
    }
}
