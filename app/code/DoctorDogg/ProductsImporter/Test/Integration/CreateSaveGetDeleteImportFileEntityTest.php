<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Test\Integration;

use \Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\TestFramework\Helper\Bootstrap;
use \PHPUnit\Framework\TestCase;

use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterface;
use \DoctorDogg\ProductsImporter\Api\Data\ImportFileInterfaceFactory;
use \DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;

use \DoctorDogg\ProductsImporter\Model\DoctorDoggProductsImporterExtensionInterface;

/**
 * Test creating, saving, deleting of import file.
 */
class CreateSaveGetDeleteImportFileEntityTest extends TestCase
{
    /**
     * @var ImportFileInterface[]
     */
    private $createdImportFiles = [];

    /**
     * @var string|null
     */
    private string|null $filePath = null;

    /**
     * @var string|null
     */
    private string|null $filePathB = null;

    /**
     * @var ObjectManagerInterface|null
     */
    private ObjectManagerInterface|null $objectManager = null;

    /**
     * @var ImportFileInterfaceRepositoryInterface|null
     */
    private ImportFileInterfaceRepositoryInterface|null $repository = null;

    /**
     * @var importFileInterfaceFactory|null
     */
    private importFileInterfaceFactory|null $importFileInterfaceFactory = null;

    /**
     * Setup.
     *
     * @return void
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->repository = $this->objectManager->get(ImportFileInterfaceRepositoryInterface::class);
        $this->importFileInterfaceFactory = $this->objectManager->get(importFileInterfaceFactory::class);

        $this->filePath = $this->prepareUniqueFilePath();
        $this->filePathB = $this->prepareUniqueFilePath();
    }

    /**
     * Test creating, saving, deleting of import files.
     *
     * @magentoDbIsolation disabled
     *
     * @return void
     * @throws AlreadyExistsException
     *
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFile::setFilePath()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFile::setIsProcessed()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFile::getFilePath()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFile::getIsProcessed()
     *
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::save()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::getFileImportInterfaceEntitiesByFilePath()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::getList()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::get()
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::delete()
     */
    public function testCreateSaveGetDeleteImportFileEntity(): void
    {
        $filePath = $this->filePath;

        /**
         * @var ImportFileInterface $importFile
         */
        $importFile = $this->importFileInterfaceFactory->create();

        $importFile->setFilePath($filePath);
        $isProcessed = false;
        $importFile->setIsProcessed($isProcessed);
        try {
            $this->repository->save($importFile);
        } catch (AlreadyExistsException $exception) {
            $getUnexpectedExceptionMessage = 'We are not expecting throwing exception here.';
            $this->assertTrue($getUnexpectedExceptionMessage);
        }
        $notGetExceptionMessage = 'We are not getting the exception';
        $this->assertTrue((bool)$notGetExceptionMessage);

        $importFileItems = $this->repository->getFileImportInterfaceEntitiesByFilePath($filePath);

        /**
         * [1]: Check that import file was saved and retrieved successfully.
         */
        $this->assertCount(1, $importFileItems);

        /**
         * @var ImportFileInterface $importFileItemFromDbTable
         */
        $importFileItemFromDbTable = \current($importFileItems);

        $this->assertSame($filePath, $importFileItemFromDbTable->getFilePath());
        $this->assertSame($isProcessed, $importFileItemFromDbTable->getIsProcessed());
        /**
         * [1][END]
         */

        $entityId = $importFileItemFromDbTable->getEntityId();
        /**
         * [2] \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::get()
         */
        $importFileById = $this->repository->get($entityId);
        $this->assertSame($filePath, $importFileById->getFilePath());
        $this->assertSame($isProcessed, $importFileById->getIsProcessed());
        /**
         * [2][END]
         */

        /**
         * [3] \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::delete()
         */
        $result = $this->repository->delete($importFileById);
        $this->assertSame(true, $result);
        /**
         * [3][END]
         */
    }

    /**
     * @magentoDbIsolation disabled
     *
     * @return void
     * @throws AlreadyExistsException
     *
     * @covers \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::deleteById()
     */
    public function testDeleteByIdImportFileEntity(): void
    {
        /**
         * @var ImportFileInterface $importFile
         */
        $importFile = $this->importFileInterfaceFactory->create();

        try {
            $this->repository->save($importFile);
        } catch (AlreadyExistsException $exception) {
            $getUnexpectedExceptionMessage = 'We are not expecting throwing exception here.';
            $this->assertTrue($getUnexpectedExceptionMessage);
        }

        /**
         * [3] \DoctorDogg\ProductsImporter\Model\ImportFileInterfaceRepository::delete()
         */
        $result = $this->repository->deleteById($importFile->getEntityId());
        $this->assertSame(true, $result);
        /**
         * [3][END]
         */
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        foreach ($this->createdImportFiles as $importFile) {
            $this->repository->delete($importFile);
        }

        parent::tearDown();
    }

    /**
     * Prepare file path which is not in DB table.
     *
     * @return string
     * @throws \Exception
     */
    private function prepareUniqueFilePath(): string
    {
        $filePath = $this->_getGeneratedFilePath();

        $importFileItems = null;
        $i = 0;

        /**
         * We need to make sure that there was no entity with this path in the database before.
         */
        while ($importFileItems === null || (\is_array($importFileItems) && \count($importFileItems) > 0)) {
            $importFileItems = $this->repository->getFileImportInterfaceEntitiesByFilePath($filePath);

            if (\is_array($importFileItems) && \count($importFileItems) > 0) {
                $filePath = $this->_getGeneratedFilePath();
            }

            $i++;
            if ($i > 999) {
                throw new \Exception('Unexpected behaviour: Can\'t generate the import file path');
            }
        }

        return $filePath;
    }

    /**
     * Get generated file path.
     *
     * @return string
     */
    private function _getGeneratedFilePath(): string
    {
        $importFileName = \uniqid(DoctorDoggProductsImporterExtensionInterface::PREFIX, true);
        $filePath = '/var/www/html/products_import_files/' . $importFileName . '.csv';

        return $filePath;
    }
}
