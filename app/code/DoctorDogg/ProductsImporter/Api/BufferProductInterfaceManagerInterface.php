<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;

/**
 * The interface that work with the buffer product entity:
 *  - markProductAsImported(): mark the product as imported
 */
interface BufferProductInterfaceManagerInterface
{
    /**
     * Mark the product as imported.
     *
     * @param int $bufferProductId
     * @return null|BufferProductInterface
     */
    public function markProductAsImported(int $bufferProductId): ?BufferProductInterface;

    /**
     * Mark the product as not imported and not planned for import to allow the future importing.
     *
     * @param int $bufferProductId
     * @return null|BufferProductInterface
     */
    public function markProductAsNotImportedNotPlannedForImport(int $bufferProductId): ?BufferProductInterface;
}
