<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Planner;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Api\ProductImportTaskPlannerInterface;
use \DoctorDogg\ProductsImporter\Api\BufferProductInterfaceRepositoryInterface;
use \DoctorDogg\ProductsImporter\Api\BufferProductProviderInterface;
use \DoctorDogg\ProductsImporter\Api\BufferTemporaryTableManagementInterface;
use \DoctorDogg\ProductsImporter\Api\ConfigReaderInterface;
use \DoctorDogg\ProductsImporter\Api\CsvFileImporterInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterface;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQTopicInterface;
use \DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;

/**
 * The planner
 *  - takes information about N products from the temporary buffer table `doctordogg_productsimporter_buffer_product`;
 *  - picked up products are marked in the temporary table with a flag `doctor_dogg_is_planned_for_import` for the row with product;
 *  - information from selected products is used to schedule tasks for RabbitMQ consumers to import these products.
 */
class ProductImportTaskPlanner implements ProductImportTaskPlannerInterface
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var BufferTemporaryTableManagementInterface
     */
    private BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface;

    /**
     * @var BufferProductInterfaceRepositoryInterface
     */
    private BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepositoryInterface;

    /**
     * @var BufferProductProviderInterface
     */
    private BufferProductProviderInterface $bufferProductProviderInterface;

    /**
     * @var PublisherInterface
     */
    private PublisherInterface $publisherInterface;

    /**
     * @var RabbitMQMessageInterfaceFactory
     */
    private RabbitMQMessageInterfaceFactory $rabbitMQMessageInterfaceFactory;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var CsvFileImporterInterface
     */
    private CsvFileImporterInterface $csvFileImporterInterface;

    /**
     * @var ImportFileInterfaceRepositoryInterface
     */
    private ImportFileInterfaceRepositoryInterface $importFileInterfaceRepositoryInterface;

    /**
     * @var ImportFileInterfaceFactory
     */
    private ImportFileInterfaceFactory $importFileInterfaceFactory;

    /**
     * @var ConfigReaderInterface
     */
    private ConfigReaderInterface $configReaderInterface;

    /**
     * @var ConfigReaderLogger
     */
    private ConfigReaderLogger $configReaderLogger;

    /**
     * Constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface
     * @param BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepositoryInterface
     * @param BufferProductProviderInterface $bufferProductProviderInterface
     * @param PublisherInterface $publisherInterface
     * @param RabbitMQMessageInterfaceFactory $rabbitMQMessageInterfaceFactory
     * @param CsvFileImporterInterface $csvFileImporterInterface
     * @param ImportFileInterfaceFactory $importFileInterfaceFactory
     * @param ImportFileInterfaceRepositoryInterface $importFileInterfaceRepositoryInterface
     * @param ConfigReaderInterface $configReaderInterface
     * @param ConfigReaderLogger $configReaderLogger
     * @param Json $json
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        BufferTemporaryTableManagementInterface $bufferTemporaryTableManagementInterface,
        BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepositoryInterface,
        BufferProductProviderInterface $bufferProductProviderInterface,
        PublisherInterface $publisherInterface,
        RabbitMQMessageInterfaceFactory $rabbitMQMessageInterfaceFactory,
        CsvFileImporterInterface $csvFileImporterInterface,
        ImportFileInterfaceFactory $importFileInterfaceFactory,
        ImportFileInterfaceRepositoryInterface $importFileInterfaceRepositoryInterface,
        ConfigReaderInterface $configReaderInterface,
        ConfigReaderLogger $configReaderLogger,
        Json $json,
        LogMessagePreparerInterface $logMessagePreparerInterface,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $resourceConnection->getConnection();

        $this->bufferTemporaryTableManagementInterface = $bufferTemporaryTableManagementInterface;

        $this->bufferProductInterfaceRepositoryInterface = $bufferProductInterfaceRepositoryInterface;
        $this->bufferProductProviderInterface = $bufferProductProviderInterface;

        $this->publisherInterface = $publisherInterface;
        $this->rabbitMQMessageInterfaceFactory = $rabbitMQMessageInterfaceFactory;

        $this->csvFileImporterInterface = $csvFileImporterInterface;

        $this->importFileInterfaceFactory = $importFileInterfaceFactory;
        $this->importFileInterfaceRepositoryInterface = $importFileInterfaceRepositoryInterface;

        $this->configReaderInterface = $configReaderInterface;
        $this->configReaderLogger = $configReaderLogger;

        $this->json = $json;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
        $this->logger = $logger;
    }

    /**
     * Run process:
     *  - check if temporary table with buffer products exists
     *  - create if not exists
     *  - check if we have buffer products for import
     *  - if we have, publish messages to RabbitMQ for importing the buffer product
     *    [One message to RabbitMQ queue for the one buffer product]
     *  - Mark the planned for import product
     *    with flag `doctor_dogg_is_planned_for_import` [BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID]
     *
     * @return void
     */
    public function startPlanningProcess(): void
    {
        /**
         * We schedule only tasks for the queue when the "Import products" switch is on.
         */
        if (!$this->configReaderLogger->getImportKnifeSwitcher(__METHOD__)) {

            return;
        }

        $this->checkAndCreateTemporaryTableExists();

        /**
         * @todo: [PRDIMP-96]: We can add cron job which will clear the table with temporary buffer products.
         *        But for the statistic it is good to leave this info for now.
         */

        /**
         * @todo: [PRDIMP-98]: Setting if we are going to log into log file or somewhere else or not.
         */

        /**
         * @todo: [PRDIMP-99]: Let's add the field for the buffer product
         * @todo: to save the time which it took to import this product.
         */

        /**
         * @todo: [PRDIMP-100]: We can add logic for getting the files for the import.
         * @todo:   a) maybe we can use one file at a time using limiting getting from the table.
         */

        /**
         * @todo: [PRDIMP-101]: We are going to prevent importing all the products mixed from different files.
         * @todo:  a) add check if all previous buffer products are processed before planing the new import file
         */

        /**
         * @todo: [PRDIMP-102]: Good thing is the order.
         * @todo: I am going to add new columns to the import file with
         * @todo: a) created_at ( to get the oldest import file first, but this is not cool technic )
         * @todo: b) updated_at ( don't really know where i am going to use this field )
         */

        /**
         * @todo: ??? [PRDIMP-103]: We can make new field for the import file to specify,
         * @todo: if all its product rows were already processed (successfully or with errors)
         */

        /**
         * @todo: [PRDIMP-104]: Probably there can be the status for the import file:
         * @todo:   a) not processed [0 -- as an initial point]
         * @todo:   b) import started
         * @todo:   c) import finished successfully
         * @todo:   d) import finished with errors
         */

        /**
         * @todo: [PRDIMP-105]: Possibility to import using the SFTP server.
         */

        /**
         * @todo: [PRDIMP-106]: Add possibility to import/update the existing products.
         */

        /**
         * @todo: [PRDIMP-107]: Checking if cron is running, show the alert message if cron is not running.
         */

        $importFilesItems = $this->importFileInterfaceRepositoryInterface->getFileImportInterfaceEntitiesNotProcessed();

        if (\count($importFilesItems) <= 0) {
            /**
             * Do nothing cause we do not have anything to be processed.
             */
        } else {

            /**
             * @TODO: For now we are processing one import file at a time.
             * @TODO: But we need the logic on  how to prevent processing the different import files mixed.
             */
            /**
             * @var ImportFileInterface $importFile
             */
            $importFile = (\is_array($importFilesItems) && \count($importFilesItems) > 0) ? \current($importFilesItems) : null;

            if (!$importFile) {
                return;
            }
            $csvFilePath = $importFile->getFilePath();

            /**
             * Import file with products to buffer table.
             */
            $this->csvFileImporterInterface->import((string)$csvFilePath);

            $importFileInterface = $this->getImportFileByFilePathOrCreateNew((string)$csvFilePath);

            $importFileInterface->setFilePath((string)$csvFilePath);
            $importFileInterface->setIsProcessed(true);
            try {
                $this->importFileInterfaceRepositoryInterface->save($importFileInterface);
            } catch (AlreadyExistsException $alreadyExistsException) {
                $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($alreadyExistsException));
            }
        }

        /**
         * @TODO: Do we need to split this into two cron-run?
         * @TODO:   a) upload all data into temporary table
         * @TODO:   b) Get data from the table to publish RabbitMQ messages
         */
        $plannedForImport = $this->checkTemporaryTableContainsProductsForImport();

        $this->publishMessageForImportBufferProducts($plannedForImport);
    }

    /**
     * Check temporary table exists.
     *
     * @actas private method
     *
     * @return void
     */
    public function checkAndCreateTemporaryTableExists()
    {
        /**
         * Checking if temporary table exists.
         */
        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: CronJob:' . ' ' . (string)\date(DATE_RFC2822)
            . ' ' . 'Checking if temporary table exists' . PHP_EOL
        );
        $isTableExists = $this->connection->isTableExists(BufferProductInterface::TABLE);

        if ($isTableExists) {
            /**
             * Return if the table exists.
             *
             * @todo: Probably we need some another logic to b here.
             */
            return;
        }

        try {
            /**
             * @todo: Add checking if table exists before trying to add it.
             * @todo: But first of all we need to know the logic how we are going
             * @todo: a) to remove the table
             * @todo: b) to create the table
             */
            $this->bufferTemporaryTableManagementInterface->createProductImportTemporaryTable();

            /**
             * @todo: Probably we should not catch the exceptions here, because we should not continue if table creation
             *        was not successful. Isn't it?
             */
        } catch (\Throwable $throwable) {
            $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));
        }
    }

    /**
     * Check if temporary table contains products for import.
     *
     * @return []BufferProductInterface
     */
    public function checkTemporaryTableContainsProductsForImport(): array
    {
        /**
         * Checking if temporary table contains products for import.
         */
        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: CronJob:' . ' ' . (string)\date(DATE_RFC2822)
            . ' ' . 'Checking if temporary table contains products for import' . PHP_EOL
        );

        $numberProductsScheduledAtTime = (int)$this->configReaderLogger
            ->getNumberProductsScheduledAtTime(__METHOD__);

        $productsNotImported = $this->bufferProductProviderInterface
            ->getBufferProductInterfaceEntitiesNotImportedNotPlannedForImport($numberProductsScheduledAtTime);

        if (\is_array($productsNotImported) && \count($productsNotImported)) {
            /**
             * Temporary table contains products for import.
             */
            $this->logger->info(
                PHP_EOL .
                '[DoctorDogg_ProductsImporter]: CronJob:' . ' ' . (string)\date(DATE_RFC2822)
                . ' ' . 'We have buffer products for import [' . \count($productsNotImported) . '] products' . PHP_EOL
            );
        } else {
            /**
             * Temporary table does not contain products for import.
             */
            $this->logger->info(
                PHP_EOL .
                '[DoctorDogg_ProductsImporter]: CronJob:' . ' ' . (string)\date(DATE_RFC2822)
                . ' ' . 'We have ZERO buffer products for import [' . 0 . '] products' . PHP_EOL
            );
        }

        return $productsNotImported;
    }

    /**
     * Publish message for import buffer products.
     *
     * @param BufferProductInterface[] $plannedForImport
     * @return void
     */
    public function publishMessageForImportBufferProducts(array $plannedForImport)
    {
        if (\is_array($plannedForImport) && \count($plannedForImport)) {
            /**
             * BufferProductInterface $bufferProduct
             */
            foreach ($plannedForImport as $bufferProduct) {
                $universalData = $this->json->serialize($bufferProduct->getData());
                $data = [
                    'universalData' => $universalData,
                ];
                /**
                 * @var RabbitMQMessageInterface $message
                 */
                $message = $this->rabbitMQMessageInterfaceFactory->create(
                    [
                        'data' => $data,
                    ]
                );

                $this->publisherInterface->publish(RabbitMQTopicInterface::TOPIC, $message);
                $bufferProduct->setIsPlannedForImport(true);

                try {
                    $this->bufferProductInterfaceRepositoryInterface->save($bufferProduct);
                } catch (\Throwable $throwable) {
                    $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));
                }
            }
        }
    }

    /**
     * Helper function: Get import file by file path or create new.
     *
     * @param string $csvFilePath
     * @return ImportFileInterface
     */
    private function getImportFileByFilePathOrCreateNew(string $csvFilePath): ImportFileInterface
    {
        /**
         * Get already existing import file to prevent creating new.
         */
        $importFilesItemsExisting = $this->importFileInterfaceRepositoryInterface
            ->getFileImportInterfaceEntitiesByFilePath(
                $csvFilePath,
                $processedOnly = false
            );

        if (\count($importFilesItemsExisting) > 0) {
            /**
             * If we have this import file in table -- we are using it.
             */
            $importFileInterface = \current($importFilesItemsExisting);
        } else {
            /**
             * Else we are creating it with factory.
             */
            $importFileInterface = $this->importFileInterfaceFactory->create();
        }

        return $importFileInterface;
    }
}
