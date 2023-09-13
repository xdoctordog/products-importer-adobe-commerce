<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;
use DoctorDogg\ProductsImporter\Model\Generator\ProductGeneratorFieldsByNumberInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Psr\Log\LoggerInterface;
use \DoctorDogg\ProductsImporter\Api\BufferTemporaryTableManagementInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Api\CsvFileImporterInterface;

/**
 * The class that imports CSV file into a temporary table for subsequent data import directly into Magento products.
 */
class CsvFileImporter implements CsvFileImporterInterface
{
    /**
     * @var BufferTemporaryTableManagementInterface
     */
    private BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var ProductGeneratorFieldsByNumberInterface
     */
    private ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface;

    /**
     * @var ConfigReaderLogger
     */
    private ConfigReaderLogger $configReaderLogger;

    /**
     * Constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface
     * @param ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface
     * @param ConfigReaderLogger $configReaderLogger
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface,
        ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface,
        ConfigReaderLogger $configReaderLogger,
        LoggerInterface $logger
    ) {
        $this->bufferTemporaryTableManagementInterface = $bufferTemporaryTableManagementInterface;
        $this->resourceConnection = $resourceConnection;
        $this->productGeneratorFieldsByNumberInterface = $productGeneratorFieldsByNumberInterface;
        $this->configReaderLogger = $configReaderLogger;
        $this->logger = $logger;
    }

    /**
     * Get prepared fields.
     *
     * @return string
     * @throws \Exception
     */
    public function getPreparedFields(): string
    {
        $numberColumnsInTemporaryBufferTable = $this->configReaderLogger
            ->getNumberColumnsInTemporaryBufferTable(__METHOD__);
        $fields = $this->productGeneratorFieldsByNumberInterface->generate((int)$numberColumnsInTemporaryBufferTable);

        $fieldNames = [];
        if (!\is_array($fields)) {
            throw new \Exception('Fields array for temporary product table is empty.');
        }

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? null;
            if (!$fieldName) {
                throw new \Exception('Field for temporary product table must have name');
            }
            $fieldNames[] = $fieldName;
        }

        return \implode(', ', $fieldNames);
    }

    /**
     * Import csv file into temporary table with number of product's fields which is equal or less than
     * number_columns_in_temporary_buffer_product_table (85 at maximum).
     *
     * @param string $csvFilePath
     * @return void
     */
    public function import(string $csvFilePath): void
    {
        try {
            $tableFields = $this->getPreparedFields();
            $this->resourceConnection->getConnection()
                ->query(
                    \sprintf(
                        "LOAD DATA INFILE '%s' INTO TABLE %s FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n' (" . $tableFields . ")",
                        $csvFilePath,
                        BufferProductInterface::TABLE
                    )
                );
        } catch (\Throwable $throwable) {
            $this->logger->critical('File: ' . $throwable->getFile() . ' '
                . 'on the line: ' . $throwable->getLine() . ' ' .
                $throwable->getMessage());
        }
    }
}
