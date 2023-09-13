<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\RabbitMQ\Processor;

use \Magento\Catalog\Api\Data\ProductInterface;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Serialize\Serializer\Json;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Api\BufferProductInterfaceManagerInterface;
use \DoctorDogg\ProductsImporter\Api\Data\RabbitMQMessageInterface;
use \DoctorDogg\ProductsImporter\Creator\Creator;
use \DoctorDogg\ProductsImporter\Model\BufferProductStatusManager;
use \DoctorDogg\ProductsImporter\Model\DoctorDoggProductsImporterExtensionInterface;
use \DoctorDogg\ProductsImporter\Model\Import\Product as ImportProduct;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;
use \DoctorDogg\ProductsImporter\Model\Mapper\GeneratedFieldToProductAttributeAdminSettingMapperInterface;
use \DoctorDogg\ProductsImporter\Validator\BufferProductValidatorInterface;
use \DoctorDogg\StopWatch\Api\StopWatchInterface;

/**
 * The class which will import one buffer product inside the Magento structure.
 */
class ImportProcessor
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparerInterface;

    /**
     * @var Json
     */
    private Json $json;

    /**
     * @var StopWatchInterface
     */
    private StopWatchInterface $stopWatchInterface;

    /**
     * @var ImportProduct
     */
    private ImportProduct $importProduct;

    /**
     * @var BufferProductInterfaceManagerInterface
     */
    private BufferProductInterfaceManagerInterface $bufferProductInterfaceManager;

    /**
     * @var ConfigReaderLogger
     */
    private ConfigReaderLogger $configReaderLogger;

    /**
     * @var BufferProductValidatorInterface
     */
    private BufferProductValidatorInterface $bufferProductValidatorInterface;

    /**
     * @var GeneratedFieldToProductAttributeAdminSettingMapperInterface
     */
    private GeneratedFieldToProductAttributeAdminSettingMapperInterface $mapper;

    /**
     * @var Creator
     */
    private readonly Creator $creator;

    /**
     * @var BufferProductStatusManager
     */
    private BufferProductStatusManager $bufferProductStatusManager;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * Constructor.
     *
     * @param ImportProduct $importProduct
     * @param BufferProductValidatorInterface $bufferProductValidatorInterface
     * @param GeneratedFieldToProductAttributeAdminSettingMapperInterface $mapper
     * @param Creator $creator
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparerInterface
     * @param Json $json
     * @param StopWatchInterface $stopWatchInterface
     * @param BufferProductInterfaceManagerInterface $bufferProductInterfaceManager
     * @param ConfigReaderLogger $configReaderLogger
     * @param BufferProductStatusManager $bufferProductStatusManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ImportProduct $importProduct,
        BufferProductValidatorInterface $bufferProductValidatorInterface,
        GeneratedFieldToProductAttributeAdminSettingMapperInterface $mapper,
        Creator $creator,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparerInterface,
        Json $json,
        StopWatchInterface $stopWatchInterface,
        BufferProductInterfaceManagerInterface $bufferProductInterfaceManager,
        ConfigReaderLogger $configReaderLogger,
        BufferProductStatusManager $bufferProductStatusManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->importProduct = $importProduct;
        $this->bufferProductValidatorInterface = $bufferProductValidatorInterface;
        $this->mapper = $mapper;
        $this->creator = $creator;
        $this->logger = $logger;
        $this->logMessagePreparerInterface = $logMessagePreparerInterface;
        $this->json = $json;
        $this->stopWatchInterface = $stopWatchInterface;
        $this->bufferProductInterfaceManager = $bufferProductInterfaceManager;
        $this->configReaderLogger = $configReaderLogger;
        $this->bufferProductStatusManager = $bufferProductStatusManager;
        $this->productRepository = $productRepository;

        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]:' . PHP_EOL .
            static::class . ' spl_object_id(): ' . \spl_object_id($this) . PHP_EOL
        );
    }

    /**
     * Import the buffer product.
     *
     * @param RabbitMQMessageInterface $rabbitMQMessageInterface
     * @return void
     * @throws LocalizedException
     */
    public function import(RabbitMQMessageInterface $rabbitMQMessageInterface)
    {
        /**
         * string $jsonData
         *  {
         *      "entity_id": "1",
         *      "doctor_dogg_is_planned_for_import": "0",
         *      "doctor_dogg_is_already_imported": "0",
         *      "dd_field_0": "ruiqqper",
         *      "dd_field_1": "simple",
         *      "dd_field_2": "RUIQQPER",
         *      "dd_field_3": "929.23",
         *      "dd_field_4": "ruiqqper",
         *      "dd_field_5": "some-unexisting-field-value",
         *      "dd_field_6": "Default",
         *      "dd_field_7": null,
         *       ...
         *      "dd_field_83": null,
         *      "dd_field_84": null
         *  }
         */
        $jsonData = $rabbitMQMessageInterface->getUniversalData();

        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: ' . (string)\date(DATE_RFC2822) . PHP_EOL
            . ' $jsonData: '
            . $jsonData . PHP_EOL
        );

        /**
         * array $data
         */
        $data = $this->json->unserialize($jsonData);

        /**
         * string $bufferProductId
         */
        $bufferProductId = $data['entity_id'] ?? null;

        if (!$this->configReaderLogger->getImportKnifeSwitcher(__METHOD__)) {
            /**
             * When knife switcher is turned off we are returning back the product to the list for processing it again.
             */
            $this->bufferProductInterfaceManager->markProductAsNotImportedNotPlannedForImport((int)$bufferProductId);

            return;
        }

        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: ' . (string)\date(DATE_RFC2822)
            . ' We are importing buffer product with entity_id: '
            . $bufferProductId
        );

        /**
         * Mapping product fields.
         *
         * We need mapped data before running validation.
         */
        $data = $this->mapper->map($data);
        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: ' . (string)\date(DATE_RFC2822) . PHP_EOL
            . ' Mapped file data: ' . PHP_EOL
            . \json_encode($data) . PHP_EOL
        );

        /**
         * We need to know the SKU for searching the product after the import.
         */
        $sku = $data['sku'] ?? null;

        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]:' . PHP_EOL .
            \get_class($this->bufferProductValidatorInterface) . ' spl_object_id(): ' . \spl_object_id($this->bufferProductValidatorInterface) . PHP_EOL
        );

        /**
         * Validate mapped product data.
         */
        $isValid = $this->bufferProductValidatorInterface->validateProduct($data);
        if (!$isValid) {
            $this->logger->info(
                PHP_EOL .
                '[DoctorDogg_ProductsImporter]: ' . (string)\date(DATE_RFC2822) . PHP_EOL
                . 'Buffer product is not valid with entity_id: '
                . $bufferProductId
            );

            /**
             * If buffer product is not valid, let's get the error aggregator
             * and save the error to the buffer product status
             */
            $this->bufferProductStatusManager
                ->saveValidationErrorsToBufferProductStatus(
                    (int)$bufferProductId,
                    $this->bufferProductValidatorInterface
                );
        }
        /**
         * This we use to re-generate the object.
         *
         * We need to prevent using the same entity each time.
         * This object should be re-created each time before it is using.
         */
        $this->bufferProductValidatorInterface = $this->creator->create(BufferProductValidatorInterface::class);
        if (!$isValid) {
            return;
        }

        $this->importProduct->addProductInfo($data);

        $this->stopWatchInterface->start();
        $this->importProduct->importProducts();
        $this->stopWatchInterface->stop();
        $deltaString = $this->stopWatchInterface->delta();

        $product = null;
        $checkProductExistsAfterImport = $this->configReaderLogger->getCheckProductExistsAfterImport(__METHOD__);
        /**
         * If we should check if we are going to check if product exists.
         */
        if (\is_string($sku) && (\mb_strlen($sku) > 0) && $checkProductExistsAfterImport) {
            try {
                $product = $this->productRepository->get($sku);
            } catch (NoSuchEntityException $noSuchEntityException) {
                /**
                 * We have no such product after import - Save to validation errors
                 */
                $errorStrings = [
                    DoctorDoggProductsImporterExtensionInterface::NAME . ':' . 'Product with sku [' . $sku . '] has not been imported'
                ];

                $this->bufferProductStatusManager
                    ->saveValidationErrorsWithErrorStrings((int)$bufferProductId, $errorStrings);

            } catch (\Throwable $throwable) {
                $this->logger->info($this->logMessagePreparerInterface->getErrorMessage($throwable));
            }
        }

        if ($checkProductExistsAfterImport) {
            /**
             * If product exists, let's mark buffer product status as existing product in Magento.
             */
            if (\is_object($product) && ($product instanceof ProductInterface) && ($product->getId() > 0)) {
                $this->bufferProductStatusManager->markExisting((int)$bufferProductId);
            } else {
                $this->bufferProductStatusManager->markNotExisting((int)$bufferProductId);
            }
        } else {
            $this->bufferProductStatusManager->markNotChecked((int)$bufferProductId);
        }

        $this->bufferProductInterfaceManager->markProductAsImported((int)$bufferProductId);

        $this->logger->info(
            PHP_EOL .
            '[DoctorDogg_ProductsImporter]: ' . ' [' . $deltaString . ' sec] ' . (string)\date(DATE_RFC2822)
            . ' Have imported buffer product with entity_id: '
            . $bufferProductId . ' | '
        );
    }
}
