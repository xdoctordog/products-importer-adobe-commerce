<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Psr\Log\LoggerInterface;
use \DoctorDogg\LogMessagePreparer\Api\LogMessagePreparerInterface;
use \DoctorDogg\ProductsImporter\Api\BufferProductInterfaceManagerInterface;
use \DoctorDogg\ProductsImporter\Api\BufferProductInterfaceRepositoryInterface;
use \DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;

/**
 * The class that work with the buffer product entity:
 *  - markProductAsImported(): mark the product as imported
 */
class BufferProductInterfaceManager implements BufferProductInterfaceManagerInterface
{
    /**
     * @var BufferProductInterfaceRepositoryInterface
     */
    private BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var LogMessagePreparerInterface
     */
    private LogMessagePreparerInterface $logMessagePreparer;

    /**
     * Constructor.
     *
     * @param BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepository
     * @param LoggerInterface $logger
     * @param LogMessagePreparerInterface $logMessagePreparer
     */
    public function __construct(
        BufferProductInterfaceRepositoryInterface $bufferProductInterfaceRepository,
        LoggerInterface $logger,
        LogMessagePreparerInterface $logMessagePreparer
    ) {
        $this->bufferProductInterfaceRepository = $bufferProductInterfaceRepository;
        $this->logger = $logger;
        $this->logMessagePreparer = $logMessagePreparer;
    }

    /**
     * Mark the product as imported.
     *
     * @param int $bufferProductId
     * @return null|BufferProductInterface
     */
    public function markProductAsImported(int $bufferProductId): ?BufferProductInterface
    {
        try {
            $bufferProductInterface = $this->bufferProductInterfaceRepository->get($bufferProductId);
            $bufferProductInterface->setIsAlreadyImported(true);
            $this->bufferProductInterfaceRepository->save($bufferProductInterface);
        } catch (NoSuchEntityException | AlreadyExistsException $exception) {
            $this->logger->info($this->logMessagePreparer->getErrorMessage($exception));
        }

        return $bufferProductInterface ?? null;
    }

    /**
     * Mark the product as not imported and not planned for import to allow the future importing.
     *
     * @param int $bufferProductId
     * @return null|BufferProductInterface
     */
    public function markProductAsNotImportedNotPlannedForImport(int $bufferProductId): ?BufferProductInterface
    {
        try {
            $bufferProductInterface = $this->bufferProductInterfaceRepository->get($bufferProductId);
            $bufferProductInterface->setIsPlannedForImport(false);
            $bufferProductInterface->setIsAlreadyImported(false);
            $this->bufferProductInterfaceRepository->save($bufferProductInterface);
        } catch (NoSuchEntityException | AlreadyExistsException $exception) {
            /**
             * @TODO: Let's think do we really need to log this.
             */
            $this->logger->info($this->logMessagePreparer->getErrorMessage($exception));
        }

        return $bufferProductInterface ?? null;
    }
}
