<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Preparator;

use DoctorDogg\ProductsImporter\Model\DoctorDoggProductsImporterExtensionInterface;

/**
 * Class which provides the value for the field which is not presented in repository.
 */
class UniqueFieldInRepositoryPreparator
{
    /**
     * @const int MAX_NUMBER_GENERATION_ATTEMPT
     */
    public const MAX_NUMBER_GENERATION_ATTEMPT = 999;

    /**
     * @TODO: Should be mapped somehow with maximum possible value of the field in database.
     *        Can be the reason of the issues during running integration tests.
     *
     * @const int BIG_INT_VALUE
     */
    public const BIG_INT_VALUE = 2147483647;

    /**
     * @const string SCALAR_INT_KEY
     */
    public const SCALAR_INT_KEY = 'int';

    /**
     * @const string SCALAR_STRING_KEY
     */
    public const SCALAR_STRING_KEY = 'string';

    /**
     * Prepare the object's unique value of the field which is not presented in DB table.
     *
     * @param object $repository
     * @param string $getByFieldMethodName
     * @param string $fieldType
     * @return mixed
     * @throws \Exception
     */
    public function getUnique(
        object $repository,
        string $getByFieldMethodName,
        string $fieldType = self::SCALAR_STRING_KEY
    ): mixed {
        $isMethodExists = \method_exists($repository, $getByFieldMethodName);
        if (!$isMethodExists) {
            throw new \Exception('Method of the repository is not exists.');
        }

        $fieldValue = $this->_getGeneratedFieldValue($fieldType);
        $items = null;

        $i = 0;

        /**
         * We need to make sure that there was no entity with this path in the database before.
         */
        while ($items === null || (\is_array($items) && \count($items) > 0)) {
            $items = $repository->$getByFieldMethodName($fieldValue);

            if (\is_array($items) && \count($items) > 0) {
                /**
                 * Regenerate field if it is presented in repository.
                 */
                $fieldValue = $this->_getGeneratedFieldValue($fieldType);
            }
            $i++;

            if ($i > static::MAX_NUMBER_GENERATION_ATTEMPT) {
                throw new \Exception('Unexpected behaviour: Can\'t generate the import file path');
            }
        }

        return $fieldValue;
    }

    /**
     * Get generated field value.
     *
     * @param string $fieldType
     * @return mixed
     * @throws \Exception
     */
    private function _getGeneratedFieldValue(string $fieldType = self::SCALAR_STRING_KEY): mixed
    {
        $fieldValue = match ($fieldType) {
            self::SCALAR_STRING_KEY => \uniqid(DoctorDoggProductsImporterExtensionInterface::PREFIX, true),
            self::SCALAR_INT_KEY => \mt_rand(1, static::BIG_INT_VALUE),
            default => throw new \Exception('Field type is not correct.'),
        };

        return $fieldValue;
    }
}
