<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Api;

/**
 * The interface which provides the buffer product interface entities.
 */
interface BufferProductProviderInterface
{
    /**
     * Get buffer product interface entities.
     *
     * @return array
     */
    public function getBufferProductInterfaceEntities(): array;

    /**
     * Get buffer product interface entities by ID.
     *
     * @param int $bufferProductInterfaceId
     * @return array
     */
    public function getBufferProductInterfaceEntitiesByEntityId(int $bufferProductInterfaceId): array;

    /**
     * Get buffer product interface entities by planned for import.
     *
     * @return array
     */
    public function getBufferProductInterfaceEntitiesNotPlannedForImport(): array;
}
