<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Plugin\Model\BufferProductInterfaceRepository\Get;

use \DoctorDogg\ProductsImporter\Api\BufferProductStatusRepositoryInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductExtensionInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use \DoctorDogg\ProductsImporter\Model\BufferProductInterfaceRepository;

/**
 * After plugin for method:
 * \DoctorDogg\ProductsImporter\Model\BufferProductInterfaceRepository::get()
 *
 * Adding extension attributes instance for it.
 */
class After
{
    /**
     * @var BufferProductStatusRepositoryInterface
     */
    private BufferProductStatusRepositoryInterface $bufferProductStatusRepositoryInterface;

    /**
     * Constructor.
     *
     * @param BufferProductStatusRepositoryInterface $bufferProductStatusRepositoryInterface
     */
    public function __construct(
        BufferProductStatusRepositoryInterface $bufferProductStatusRepositoryInterface
    ) {
        $this->bufferProductStatusRepositoryInterface = $bufferProductStatusRepositoryInterface;
    }

    /**
     * After plugin method.
     *
     * @param BufferProductInterfaceRepository $subject
     * @param BufferProductInterface $result
     * @return BufferProductInterface
     */
    public function afterGet(
        BufferProductInterfaceRepository $subject,
        BufferProductInterface $result
    ):BufferProductInterface {
        $bufferProductId = $result->getEntityId();

        $bufferProductStatus = $this->bufferProductStatusRepositoryInterface->getOneByBufferProductId($bufferProductId);

        if (!$bufferProductStatus) {
            return $result;
        }

        $productExistsAfterImportStatus = $bufferProductStatus->getProductExistsAfterImportStatus();
        $validationErrors = $bufferProductStatus->getValidationErrors();

        /**
         * @var BufferProductExtensionInterface $extensionAttributes
         */
        $extensionAttributes = $result->getExtensionAttributes();

        $extensionAttributes->setProductExistsAfterImportStatus($productExistsAfterImportStatus);
        $extensionAttributes->setValidationErrors($validationErrors);

        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
