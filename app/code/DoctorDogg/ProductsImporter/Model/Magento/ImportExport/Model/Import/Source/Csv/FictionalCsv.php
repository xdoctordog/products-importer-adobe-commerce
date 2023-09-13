<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model\Magento\ImportExport\Model\Import\Source\Csv;

use \Magento\ImportExport\Model\Import\AbstractSource;

/**
 * The class which will help us with validating process.
 * It will simulate the behaviour of the CSV Source.
 *
 * But we can also say that this class is just another Source,
 * without saying that it is simulating the CSV source.
 * It is just another source.
 *
 * @analogue: Magento\ImportExport\Model\Import\Source\Csv
 */
class FictionalCsv extends AbstractSource
{
    /**
     * @var array
     */
    protected $columnNames = [];

    /**
     * @var array
     */
    private $productsInfo = [];

    /**
     * @var int
     */
    private $iPointer = 0;

    /**
     * Constructor.
     *
     * @param array $columnNames
     */
    public function __construct(
        array $columnNames
    ) {
        $this->columnNames = $columnNames;
    }

    /**
     * Add product info to array.
     *
     * @param $productInfo
     * @return void
     */
    public function addProductInfo($productInfo)
    {
        $this->productsInfo[] = $productInfo;
    }

    /**
     * Column names getter.
     *
     * @return array
     */
    public function getColNames()
    {
        return $this->columnNames;
    }

    /**
     * Get next row with product info.
     *
     * @return array|bool
     */
    protected function _getNextRow()
    {
        return $this->productsInfo[++$this->iPointer] ?? false;
    }

    /**
     * Return the current product info.
     *
     * @return array
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return array_combine($this->columnNames, $this->productsInfo[$this->iPointer]);
    }

    /**
     * Move forward to next product info.
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->iPointer++;
    }

    /**
     * Return the key of the current product info.
     *
     * @return int -1 if out of bounds, 0 or more otherwise
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->iPointer;
    }

    /**
     * Rewind.
     *
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->iPointer = 0;
    }

    /**
     * Checks if current position is valid (\Iterator interface)
     *
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset($this->productsInfo[$this->iPointer]);
    }

    /**
     * Seeks to a position (Seekable interface)
     *
     * @param int $position The position to seek to 0 or more
     * @return void
     * @throws \OutOfBoundsException
     */
    #[\ReturnTypeWillChange]
    public function seek($position)
    {
        if (isset($this->productsInfo[$position])) {
            return;
        }

        throw new \OutOfBoundsException('Please correct the seek position.');
    }
}
