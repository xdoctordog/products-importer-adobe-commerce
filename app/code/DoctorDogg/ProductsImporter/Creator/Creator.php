<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Creator;

use \Magento\Framework\ObjectManagerInterface;

/**
 * Creator of the object by its class name,
 */
class Creator
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * Constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create object of the provided class.
     *
     * @param string $className
     * @return object|null
     */
    public function create(string $className): object|null
    {
        $object = null;
        try {
            $object = $this->objectManager->create($className);
        } catch (\Throwable $throwable) {
            /**
             * Nothing to do on exception.
             */
        }

        return $object;
    }
}
