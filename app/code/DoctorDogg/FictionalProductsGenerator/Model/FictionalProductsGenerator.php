<?php

declare(strict_types=1);

namespace DoctorDogg\FictionalProductsGenerator\Model;

use Psr\Log\LoggerInterface;
use DoctorDogg\FictionalProductsGenerator\Api\ProductsGeneratorInterface;

/**
 * The class that generates fictional products.
 */
class FictionalProductsGenerator implements ProductsGeneratorInterface
{
    /**
     * @const []string
     */
    public const ENGLISH_ALPHABET = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
    ];

    /**
     * @const int SKU_LENGTH
     */
    public const SKU_LENGTH = 8;

    /**
     * @const string CSV_SEPARATOR
     */
    public const COMMA_CSV_SEPARATOR = ',';

    /**
     * All generated skus.
     *
     * @var []['generated_sku' => true]
     */
    private array $allGenaratedSkus = [];

    /**
     * Private method for generating sku.
     *
     * @return string
     */
    private function _GenerateSku(): string
    {
        $sku = '';
        $count = \count(self::ENGLISH_ALPHABET);
        for ($i = 0; $i < self::SKU_LENGTH; $i ++) {
            $randomKey = 0;
            try {
                $randomKey = \random_int(0, $count - 1);
            } catch (\Throwable $throwable) {

            }

            $sku .= self::ENGLISH_ALPHABET[$randomKey];
        }

        return $sku;
    }

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * Generate sku.
     *
     * @return string
     */
    public function generateSku(): string
    {
        $sku = $this->_GenerateSku();
        while (isset($this->allGenaratedSkus[$sku])) {
            $sku = $this->_GenerateSku();
        }
        $this->allGenaratedSkus[$sku] = true;

        return $sku;
    }

    /**
     * Generate price.
     *
     * @return float|null
     */
    public function generatePrice(): ?float
    {
        $price = null;
        try {
            $integerPartOfNumber = \random_int(1, 1000);
            $fractionalPartOfNumber = \random_int(0, 99) / 100;
            $price = $integerPartOfNumber + $fractionalPartOfNumber;
        } catch (\Throwable $throwable) {
            $this->logger->info(
                'File: ' . $throwable->getFile() . ' '
                . 'on the line: ' . $throwable->getLine() . ' '
                . $throwable->getMessage()
            );
        }

        return $price;
    }

    /**
     * Generate fictional product.
     *
     * @return string[]
     */
    public function generateFictionalProduct(): array
    {
        /**
         * As far as I know, the following product attributes are mandatory,
         * and therefore I prefer to generate a file in which the products will have these attributes.
         *
         * sku
         * product_type => simple
         * name
         * price
         * url_key
         * _attribute_set
         */
        $sku = $this->generateSku();
        $product = [
            'sku' => $sku,
            'product_type' => 'simple',
            'name' => mb_strtoupper($sku),
            'price' => (string)$this->generatePrice(),
            'url_key' => $sku,
            '_attribute_set' => 'Default'
        ];

        return $product;
    }

    /**
     * Generate fictional products.
     *
     * @return array
     */
    public function generate(): array
    {
        $products = [];
        for ($i = 0; $i < 5; $i ++) {
            $products[] = $this->generateFictionalProduct();
        }

        return $products;
    }

    /**
     * Generate csv content.
     *
     * @param array $products
     * @return string
     */
    public function generateCsvContent(array $products): string
    {
        $csvContent = '';
        foreach ($products as $product) {
            /**
             * sku
             * product_type => simple
             * name
             * price
             * url_key
             * _attribute_set
             */
            $oneLineProduct = $product['sku'] . self::COMMA_CSV_SEPARATOR
            . $product['product_type'] . self::COMMA_CSV_SEPARATOR
            . $product['name'] . self::COMMA_CSV_SEPARATOR
            . $product['price'] . self::COMMA_CSV_SEPARATOR
            . $product['url_key'] . self::COMMA_CSV_SEPARATOR
            . $product['_attribute_set'];

            $csvContent .= $oneLineProduct . PHP_EOL;
        }

        return $csvContent;
    }
}
