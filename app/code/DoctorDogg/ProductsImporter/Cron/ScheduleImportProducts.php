<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Cron;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use DoctorDogg\ProductsImporter\Api\BufferProductProviderInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterface;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQTopicInterface;

use \DoctorDogg\ProductsImporter\Api\ProductImportTaskPlannerInterface;

/**
 * Repository.
 */
use DoctorDogg\ProductsImporter\Api\BufferProductInterfaceRepositoryInterface;

/**
 * Schedule cron tasks for the queue for importing products from a temporary table with products in Magento.
 */
class ScheduleImportProducts
{
    /**
     * @var ProductImportTaskPlannerInterface
     */
    private ProductImportTaskPlannerInterface $productImportTaskPlannerInterface;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param ProductImportTaskPlannerInterface $productImportTaskPlannerInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductImportTaskPlannerInterface $productImportTaskPlannerInterface,
        LoggerInterface $logger
    ) {
        $this->productImportTaskPlannerInterface = $productImportTaskPlannerInterface;
        $this->logger = $logger;
    }

    /**
     * Start planning process.
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Cronjob starts.
         */
        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: CronJob:' . ' ' . (string)\date(DATE_RFC2822) . PHP_EOL
        );
        $this->productImportTaskPlannerInterface->startPlanningProcess();
    }
}
