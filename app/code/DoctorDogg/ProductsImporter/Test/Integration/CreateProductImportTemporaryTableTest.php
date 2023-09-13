<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Test\Integration;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\TestFramework\Helper\Bootstrap;
use \PHPUnit\Framework\TestCase;

use \Magento\Framework\App\ResourceConnection;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \DoctorDogg\ProductsImporter\Api\BufferTemporaryTableManagementInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;

/**
 * Test creation of `doctordogg_productsimporter_buffer_product` table.
 */
class CreateProductImportTemporaryTableTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ObjectManagerInterface|null $objectManager = null;

    /**
     * @var BufferTemporaryTableManagementInterface
     */
    private $bufferTemporaryTableManagement;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * Setup.
     *
     * @return void
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->bufferTemporaryTableManagement = $this->objectManager->get(BufferTemporaryTableManagementInterface::class);
        $this->connection = $this->objectManager->get(ResourceConnection::class)->getConnection();
    }

    /**
     * Test creation of `doctordogg_productsimporter_buffer_product` table.
     *
     * @magentoDbIsolation disabled
     *
     * @return void
     * @throws \Zend_Db_Exception
     *
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFile::setFilePath()
     */
    public function testCreateSaveGetDeleteImportFileEntity(): void
    {
        $isTableExists = $this->connection->isTableExists(BufferProductInterface::TABLE);

        $backUpTableName = 'backup_' . BufferProductInterface::TABLE;

        /**
         * @TODO: Need we add checking if all the columns are added to the table.
         */
        if ($isTableExists) {
            $this->connection->renameTable(BufferProductInterface::TABLE, $backUpTableName);
        }

        $this->bufferTemporaryTableManagement->createProductImportTemporaryTable();

        $isTableExistsB = $this->connection->isTableExists(BufferProductInterface::TABLE);
        $this->assertTrue($isTableExistsB);

        if ($isTableExistsB) {
            $this->connection->dropTable(BufferProductInterface::TABLE);
            $this->connection->renameTable($backUpTableName, BufferProductInterface::TABLE);
        }
    }
}
