<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Helper;

use DoctorDogg\ProductsImporter\Api\ImportFileInterfaceRepositoryInterface;

/**
 * Helper class: To get the unique file path which is really not presented in DB table.
 */
class GetImportFileWithUniqueFilePath
{
    /**
     * @const string MAX_ATTEMPTS_NUMBER
     */
    public const MAX_ATTEMPTS_NUMBER = 9999;

    /**
     * @var ImportFileInterfaceRepositoryInterface
     */
    private ImportFileInterfaceRepositoryInterface $importFileRepository;

    /**
     * Constructor.
     *
     * @param ImportFileInterfaceRepositoryInterface $importFileRepository
     */
    public function __construct(
        ImportFileInterfaceRepositoryInterface $importFileRepository
    ) {
        $this->importFileRepository = $importFileRepository;
    }

    /**
     * Prepare file path which is not in DB table.
     *
     * @return string
     * @throws \Exception
     */
    public function getUniqueFilePath(): string
    {
        $importFileName = \uniqid('doctordogg-productsimporter-', true);

        $filePath = '/var/www/html/products_import_files/' . $importFileName . '.csv';
        $importFileItems = null;

        $i = 0;

        /**
         * We need to make sure that there was no entity with this path in the database before.
         */
        while ($importFileItems === null || (\is_array($importFileItems) && \count($importFileItems) > 0)) {
            $importFileItems = $this->importFileRepository->getFileImportInterfaceEntitiesByFilePath($filePath);

            $i++;
            if ($i > static::MAX_ATTEMPTS_NUMBER) {
                throw new \Exception('Unexpected behaviour: Can\'t generate the import file path');
            }
        }

        return $filePath;
    }
}
