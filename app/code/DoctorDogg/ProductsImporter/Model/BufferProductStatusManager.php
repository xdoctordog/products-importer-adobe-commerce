<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use DoctorDogg\ProductsImporter\Api\BufferProductStatusRepositoryInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterface;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductStatusInterfaceFactory;
use DoctorDogg\ProductsImporter\Validator\BufferProductValidatorInterface;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * Manager of buffer product status.
 *  - save validation errors using buffer product Validator
 *  - save validation errors using the input error strings as the array
 *  - save the `product_exists_after_import_status` for buffer product status
 */
class BufferProductStatusManager
{
    /**
     * @var BufferProductStatusInterfaceFactory
     */
    private BufferProductStatusInterfaceFactory $bufferProductStatusInterfaceFactory;

    /**
     * @var BufferProductStatusRepositoryInterface
     */
    private BufferProductStatusRepositoryInterface $bufferProductStatusRepository;

    /**
     * Constructor.
     *
     * @param BufferProductStatusInterfaceFactory $bufferProductStatusInterfaceFactory
     * @param BufferProductStatusRepositoryInterface $bufferProductStatusRepository
     */
    public function __construct(
        BufferProductStatusInterfaceFactory $bufferProductStatusInterfaceFactory,
        BufferProductStatusRepositoryInterface $bufferProductStatusRepository
    ) {
        $this->bufferProductStatusInterfaceFactory = $bufferProductStatusInterfaceFactory;
        $this->bufferProductStatusRepository = $bufferProductStatusRepository;
    }

    /**
     * Save validation errors.
     *
     * @param int $bufferProductId
     * @param BufferProductValidatorInterface $bufferProductValidatorInterface
     * @return bool
     */
    public function saveValidationErrorsToBufferProductStatus(
        int $bufferProductId,
        BufferProductValidatorInterface $bufferProductValidatorInterface
    ): bool {
        $result = false;

        $errorAggregator = $bufferProductValidatorInterface->getErrorAggregator();
        if (!$errorAggregator) {
            return $result;
        }

        $errorStrings = [];
        $allErrors = $errorAggregator->getAllErrors();
        if (!\is_array($allErrors) || !\is_iterable($allErrors)) {
            return $result;
        }

        foreach ($allErrors as $error) {
            $errorStrings[] = $error->getErrorCode() . ': ' . $error->getErrorMessage();
        }

        return $this->saveValidationErrorsWithErrorStrings($bufferProductId, $errorStrings);
    }

    /**
     * Save validation errors based on the error strings.
     *
     * @param int $bufferProductId
     * @param array $errorStrings
     * @return bool
     */
    public function saveValidationErrorsWithErrorStrings(
        int $bufferProductId,
        array $errorStrings
    ): bool {
        $result = false;

        if (!\count($errorStrings)) {
            return $result;
        }

        $bufferProductStatus = $this->initBufferProductStatus($bufferProductId);

        $oldValidationErrorStrings = $bufferProductStatus->getValidationErrors();

        $errorStrings = \array_unique(\array_merge($errorStrings, $oldValidationErrorStrings));
        $bufferProductStatus->setValidationErrors($errorStrings);

        return $this->save($bufferProductStatus);
    }

    /**
     * Mark product as existing after imported.
     *
     * @param int $bufferProductId
     * @return bool
     */
    public function markExisting(int $bufferProductId): bool
    {
        return $this->markBufferProductStatusAfterImporting(
            $bufferProductId,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_EXISTS
        );
    }

    /**
     * Mark product as not existing after imported.
     *
     * @param int $bufferProductId
     * @return bool
     */
    public function markNotExisting(int $bufferProductId): bool
    {
        return $this->markBufferProductStatusAfterImporting(
            $bufferProductId,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_EXIST
        );
    }

    /**
     * Mark product as not existing after imported.
     *
     * @param int $bufferProductId
     * @return bool
     */
    public function markNotChecked(int $bufferProductId): bool
    {
        return $this->markBufferProductStatusAfterImporting(
            $bufferProductId,
            BufferProductStatusInterface::CHECK_PRODUCT_EXISTS_AFTER_IMPORT__PRODUCT_DOES_NOT_CHECKED
        );
    }

    /**
     * Set buffer product status after importing.
     *
     * @param int $bufferProductId
     * @param int $status
     * @return bool
     */
    private function markBufferProductStatusAfterImporting(
        int $bufferProductId,
        int $status
    ): bool {
        $bufferProductStatus = $this->initBufferProductStatus($bufferProductId);
        $bufferProductStatus->setProductExistsAfterImportStatus($status);

        return $this->save($bufferProductStatus);
    }

    /**
     * Save buffer product status.
     *
     * @param $bufferProductStatus
     * @return bool
     */
    private function save($bufferProductStatus)
    {
        $result = false;
        try {
            $this->bufferProductStatusRepository->save($bufferProductStatus);
            $result = true;
        } catch (AlreadyExistsException $exception) {
            /**
             * Should not be thrown because we have removed this entity before.
             */
        }

        return $result;
    }

    /**
     * Init buffer product status.
     *
     * @param int $bufferProductId
     * @return BufferProductStatusInterface
     */
    private function initBufferProductStatus(int $bufferProductId): BufferProductStatusInterface
    {
        $bufferProductStatuses = $this->bufferProductStatusRepository->getByBufferProductId($bufferProductId);
        if (\count($bufferProductStatuses) > 0) {
            /**
             * @var BufferProductStatusInterface $bufferProductStatus
             */
            $bufferProductStatus = \current($bufferProductStatuses);
        } else {
            $bufferProductStatus = $this->bufferProductStatusInterfaceFactory->create();
            $bufferProductStatus->setBufferProductId($bufferProductId);
        }

        return $bufferProductStatus;
    }
}
