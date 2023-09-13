<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Ui\DataProvider\ImportFile;

use \Magento\Framework\Api\Filter;
use \Magento\Framework\App\ObjectManager;
use \Magento\Store\Model\Store;
use \Magento\Ui\DataProvider\AbstractDataProvider;
use \Magento\Ui\DataProvider\Modifier\ModifierInterface;
use \Magento\Ui\DataProvider\Modifier\PoolInterface;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile\Collection as ImportFileCollection;
use \DoctorDogg\ProductsImporter\Model\ResourceModel\ImportFile\CollectionFactory as ImportFileCollectionFactory;

/**
 * GRID provider of the import files.
 */
class ImportFileProvider extends AbstractDataProvider
{
    /**
     * Product collection
     *
     * @var ImportFileCollection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var PoolInterface
     */
    private $modifiersPool;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ImportFileCollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $modifiersPool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ImportFileCollectionFactory $collectionFactory,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = [],
        PoolInterface $modifiersPool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->modifiersPool = $modifiersPool ?: ObjectManager::getInstance()->get(PoolInterface::class);
    }

    /**
     * Get data.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $data = $this->getCollection()->toArray();

        /** @var ModifierInterface $modifier */
        foreach ($this->modifiersPool->getModifiersInstances() as $modifier) {
            $data = $modifier->modifyData($data);
        }
        return $data;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        /**
         * Nothing to do on filtering by field 'store_id'
         */
        if ($filter->getField() === 'store_id') {
            return;
        }

        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
