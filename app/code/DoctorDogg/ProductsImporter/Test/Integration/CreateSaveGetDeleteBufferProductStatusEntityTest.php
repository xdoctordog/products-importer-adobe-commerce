<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Test\Integration;

use \Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Serialize\SerializerInterface;
use \Magento\TestFramework\Helper\Bootstrap;
use \PHPUnit\Framework\TestCase;

/**
 * Model interface.
 */
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface as ModelInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterfaceFactory as ModelInterfaceFactory;

/**
 * Repository
 */
use \DoctorDogg\ProductsImporter\Api\BufferProductStatusRepositoryInterface as Repository;

/**
 * Unique field value generator.
 */
use \DoctorDogg\ProductsImporter\Model\Preparator\UniqueFieldInRepositoryPreparator;

/**
 * Test creating, saving, deleting of buffer product statuses.
 */
class CreateSaveGetDeleteBufferProductStatusEntityTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ObjectManagerInterface|null $objectManager = null;

    /**
     * @var Repository|null
     */
    private Repository|null $repository = null;

    /**
     * @var ModelInterfaceFactory|null
     */
    private ModelInterfaceFactory|null $modelFactory = null;

    /**
     * @var UniqueFieldInRepositoryPreparator|null
     */
    private UniqueFieldInRepositoryPreparator|null $uniqueFieldInRepositoryPreparator = null;

    /**
     * @var SerializerInterface|null
     */
    private SerializerInterface|null $serializerInterface = null;

    /**
     * Setup.
     *
     * @return void
     * @throws \Exception
     */
    protected function setUp(): void
    {
        /**
         * @TODO: I am going to abandon approach use setUp() method.
         */
        $this->objectManager = Bootstrap::getObjectManager();
        $this->repository = $this->objectManager->get(Repository::class);
        $this->modelFactory = $this->objectManager->get(ModelInterfaceFactory::class);
        $this->uniqueFieldInRepositoryPreparator = $this->objectManager->get(UniqueFieldInRepositoryPreparator::class);
        $this->serializerInterface = $this->objectManager->get(SerializerInterface::class);
    }

    /**
     * Test creating, saving, deleting of buffer product statuses.
     *
     * @magentoDbIsolation disabled
     *
     * @return void
     *
     * @caution: CAUTION! Next throws statement only shows that test fails.
     *        This method-test does not throw exception if everything is okay.
     * @throws NoSuchEntityException
     *
     * @covers \DoctorDogg\ProductsImporter\Model\BufferProductStatusRepository::save()
     * @covers \DoctorDogg\ProductsImporter\Model\BufferProductStatusRepository::get()
     *
     * @covers \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface::getEntityId()
     *
     * @covers \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface::getBufferProductId()
     * @covers \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface::setBufferProductId()
     *
     * @covers \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface::getProductExistsAfterImportStatus()
     * @covers \DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface::setProductExistsAfterImportStatus()
     */
    public function testCreateSaveGetDeleteBufferProductStatusEntity(): void
    {
        /**
         * @var ModelInterface $bufferProductStatus
         */
        $bufferProductStatus = $this->modelFactory->create();

        $getByFieldMethodName = 'getByBufferProductId';
        $uniqueBufferProductId = $this->uniqueFieldInRepositoryPreparator->getUnique(
            $this->repository,
            $getByFieldMethodName,
            UniqueFieldInRepositoryPreparator::SCALAR_INT_KEY
        );

        $bufferProductStatus->setProductExistsAfterImportStatus(
            ModelInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS
        );
        /**
         * Same value of status as was set previously.
         */
        $this->assertSame(
            ModelInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS,
            $bufferProductStatus->getProductExistsAfterImportStatus()
        );

        $bufferProductStatus->setBufferProductId($uniqueBufferProductId);
        /**
         * Same value of buffer product ID as was set previously.
         */
        $this->assertSame(
            $uniqueBufferProductId,
            $bufferProductStatus->getBufferProductId()
        );

        $validationErrors = [
            'some error text',
            'some another error text'
        ];

        $bufferProductStatus->setValidationErrors($validationErrors);

        /**
         * The same validation errors checking.
         */
        $this->assertSame(
            $validationErrors,
            $bufferProductStatus->getValidationErrors()
        );

        try {
            $this->repository->save($bufferProductStatus);
        } catch (AlreadyExistsException $alreadyExistsException) {
            $getUnexpectedExceptionMessage =
                'We are creating unique object and are not expecting throwing exception here. '
                . 'Probably DB table is used by other processes.';
            $this->assertTrue($getUnexpectedExceptionMessage);
        }
        $notGetExceptionMessage = 'We are not getting the exception';
        $this->assertTrue((bool)$notGetExceptionMessage);

        /**
         * Check the new value of the entity_id. It should be updated after saving in db.
         */
        $this->assertTrue($bufferProductStatus->getEntityId() > 0);

        $bufferProductStatusFromRepository = $this->repository->get($bufferProductStatus->getEntityId());

        /**
         * The same entity ID checking.
         */
        $this->assertSame(
            $bufferProductStatus->getEntityId(),
            $bufferProductStatusFromRepository->getEntityId()
        );

        /**
         * The same buffer product ID checking.
         */
        $this->assertSame(
            $bufferProductStatus->getBufferProductId(),
            $bufferProductStatusFromRepository->getBufferProductId()
        );

        /**
         * The same validation errors checking.
         */
        $this->assertSame(
            $bufferProductStatus->getValidationErrors(),
            $bufferProductStatusFromRepository->getValidationErrors()
        );

        /**
         * The same status checking.
         */
        $this->assertSame(
            $bufferProductStatus->getProductExistsAfterImportStatus(),
            $bufferProductStatusFromRepository->getProductExistsAfterImportStatus()
        );

        /**
         * The same validation errors checking.
         */
        $this->assertSame(
            $bufferProductStatus->getValidationErrors(),
            $bufferProductStatusFromRepository->getValidationErrors()
        );

        $deleteResult = $this->repository->delete($bufferProductStatus);
        /**
         * Check if delete method return true.
         */
        $this->assertTrue($deleteResult);
    }

    /**
     * Test deleteByBufferProductId method.
     *
     * @magentoDbIsolation disabled
     *
     * @return void
     *
     * @caution: CAUTION! Next throws statement only shows that test fails.
     *        This method-test does not throw exception if everything is okay.
     * @throws AlreadyExistsException
     *
     * @covers \DoctorDogg\ProductsImporter\Model\BufferProductStatusRepository::deleteByBufferProductId()
     */
    public function testDeleteByBufferProductId()
    {
        /**
         * @var ModelInterface $bufferProductStatus
         */
        $bufferProductStatus = $this->modelFactory->create();

        $getByFieldMethodName = 'getByBufferProductId';
        $uniqueBufferProductId = $this->uniqueFieldInRepositoryPreparator->getUnique(
            $this->repository,
            $getByFieldMethodName,
            UniqueFieldInRepositoryPreparator::SCALAR_INT_KEY
        );

        $bufferProductStatus->setBufferProductId($uniqueBufferProductId);

        $this->repository->save($bufferProductStatus);

        /**
         * Save the entity id.
         */
        $bufferProductStatusId = $bufferProductStatus->getEntityId();

        $deleteResult = $this->repository->deleteByBufferProductId($bufferProductStatus->getBufferProductId());

        /**
         * Check if deleteByBufferProductId method return true.
         */
        $this->assertTrue($deleteResult);

        $bufferProductStatusFromRepository = $this->repository->get($bufferProductStatusId);

        $this->assertNotSame($bufferProductStatusId, $bufferProductStatusFromRepository->getEntityId());
    }
}
