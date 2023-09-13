<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Psr\Log\LoggerInterface;
use DoctorDogg\ProductsImporter\Api\BufferTemporaryTableManagementInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Api\RequiredProductAttributesInterface;
use \DoctorDogg\ProductsImporter\Model\Config\Reader\ConfigReader\ConfigReaderLoggerDecorator as ConfigReaderLogger;
use \DoctorDogg\ProductsImporter\Model\Generator\ProductGeneratorFieldsByNumberInterface;

/**
 * The class that create the table for the for buffer products:
 *  - Gets the hardcoded required fields
 */
class BufferTemporaryTableManagement implements BufferTemporaryTableManagementInterface
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Generator of the temporary product's fields.
     *
     * @var ProductGeneratorFieldsByNumberInterface
     */
    private ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface;

    /**
     * Config reader which is wrapped with the logger to log values getting from the config.
     *
     * @var ConfigReaderLogger
     */
    private ConfigReaderLogger $configReaderLogger;

    /**
     * Constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface
     * @param ConfigReaderLogger $configReaderLogger
     * @param LoggerInterface $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ProductGeneratorFieldsByNumberInterface $productGeneratorFieldsByNumberInterface,
        ConfigReaderLogger $configReaderLogger,
        LoggerInterface $logger
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->productGeneratorFieldsByNumberInterface = $productGeneratorFieldsByNumberInterface;
        $this->resourceConnection = $resourceConnection;
        $this->configReaderLogger = $configReaderLogger;
        $this->logger = $logger;
    }

    /**
     * @deprecated
     *
     * Get array of required fields which are presented inside the import file.
     *
     * @return array[]
     * @todo: Probably this will be moved or removed at all.
     *
     * @todo: Fields can be retrieved from the admin settings -- Maybe only the names for the fields.
     * @todo: Maybe This method is another responsibility because it is used in two classes. Pls move to another class.
     */
    public function getFields(): array
    {
        $fields = [
            [
                'name' => RequiredProductAttributesInterface::SKU,
                'type' => Table::TYPE_TEXT,
                'size' => 255
            ],
            [
                'name' => RequiredProductAttributesInterface::PRODUCT_TYPE,
                'type' => Table::TYPE_TEXT,
                'size' => 255
            ],
            [
                'name' => RequiredProductAttributesInterface::NAME,
                'type' => Table::TYPE_TEXT,
                'size' => 255
            ],
            [
                'name' => RequiredProductAttributesInterface::PRICE,
                'type' => Table::TYPE_DECIMAL,
                'size' => [10, 7]
            ],
            [
                'name' => RequiredProductAttributesInterface::URL_KEY,
                'type' => Table::TYPE_TEXT,
                'size' => 255
            ],
            [
                'name' => RequiredProductAttributesInterface::_ATTRIBUTE_SET,
                'type' => Table::TYPE_TEXT,
                'size' => 255
            ],
        ];

        return $fields;
    }

    /**
     * Create temporary table where data for importing products will be stored.
     *
     * @return void
     * @throws \Exception
     * @throws \Zend_Db_Exception
     */
    public function createProductImportTemporaryTable()
    {
        $numberColumnsInTemporaryBufferTable = $this->configReaderLogger
            ->getNumberColumnsInTemporaryBufferTable(__METHOD__);
        $fields = $this->productGeneratorFieldsByNumberInterface->generate((int)$numberColumnsInTemporaryBufferTable);

        $table = $this->connection->newTable(BufferProductInterface::TABLE);

        /**
         * Adding AUTO_INCREMENT field for easy fetching products from table.
         */
        $table->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            10,
            [
                'auto_increment' => true,
                Table::OPTION_PRIMARY => true,
                Table::OPTION_UNSIGNED => true,
                Table::OPTION_NULLABLE => false
            ],
            'Entity ID'
        );

        /**
         * The field that will mark a record in temporary table with information
         * about a product that has already been planned for import.
         */
        $table->addColumn(
            BufferProductInterface::IS_PLANNED_FOR_IMPORT_ID,
            Table::TYPE_BOOLEAN,
            null,
            [
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => false,
            ],
            'Is planned for import'
        );

        /**
         * The field that will mark a record in temporary table with information
         * about a product that has already been imported.
         * We are trying to use this field to filter getting products which are not imported yet.
         */
        $table->addColumn(
            BufferProductInterface::IS_ALREADY_IMPORTED_KEY,
            Table::TYPE_BOOLEAN,
            null,
            [
                Table::OPTION_NULLABLE => false,
                Table::OPTION_DEFAULT => false,
            ],
            'Is already imported'
        );

        if (!\is_array($fields)) {
            throw new \Exception('$fields is not an array');//@todo: Add throwing exceptions using factory
        }
        if (!\count($fields)) {
            throw new \Exception('$fields is empty array');//@todo: Add throwing exceptions using factory
        }

        foreach ($fields as $field) {
            $name = $field['name'] ?? null;
            $type = $field['type'] ?? null;
            $size = $field['size'] ?? null;
            if ($name === null
                || $type === null
                || $size === null
            ) {
                continue;
            }

            /**
             * @TODO: Let's add comment for the field using argument $comment, but it is not important
             */
            $table->addColumn($name, $type, $size, $options = [], $comment = 'Comment for ' . $name);
        }

        /**
         * @TODO: We need logic on when we are going to re-create the table.
         * @TODO: i should think about it.
         *
         * $this->connection->dropTable(BufferProductInterface::TABLE);
         */
        $this->connection->createTable($table);
    }
}
