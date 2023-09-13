<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct;

use \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use \Magento\Framework\Data\Collection\EntityFactoryInterface;
use \Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use \Magento\Framework\DB\Adapter\AdapterInterface;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DoctorDogg\ProductsImporter\Api\Data\BufferProductInterface;
use DoctorDogg\ProductsImporter\Model\ResourceModel\BufferProduct as BufferProductResource;
use DoctorDogg\ProductsImporter\Model\BufferProduct;

use \Psr\Log\LoggerInterface;

/**
 * Collection class for buffer products.
 */
class Collection extends AbstractCollection
{
    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $joinProcessor;

    /**
     * @var string $_idFieldName
     */
    protected $_idFieldName = BufferProductInterface::ENTITY_ID;

    /**
     * Constructor.
     *
     * @param JoinProcessorInterface $joinProcessor
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        JoinProcessorInterface $joinProcessor,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->joinProcessor = $joinProcessor;
    }

    /**
     * Initialize resources.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(BufferProduct::class, BufferProductResource::class);
    }

    /**
     * Load data
     *
     * @param   bool $printQuery
     * @param   bool $logQuery
     * @return  $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        $this->joinProcessor->process($this);
        return parent::load($printQuery, $logQuery);
    }
}
