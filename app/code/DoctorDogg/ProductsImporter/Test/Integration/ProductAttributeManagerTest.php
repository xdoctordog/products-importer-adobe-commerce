<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Test\Integration;

use \Magento\Framework\ObjectManagerInterface;
use \Magento\TestFramework\Helper\Bootstrap;
use \PHPUnit\Framework\TestCase;

use \DoctorDogg\ProductsImporter\Model\ProductAttributeManager;

/**
 * Test for the Manager of the product attributes.
 */
class ProductAttributeManagerTest extends TestCase
{
    /**
     * @var ObjectManagerInterface|null
     */
    private ObjectManagerInterface|null $objectManager = null;

    /**
     * @var ProductAttributeManager|null
     */
    private ProductAttributeManager|null $productAttributeManager = null;

    /**
     * Setup.
     *
     * @return void
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productAttributeManager = $this->objectManager->get(ProductAttributeManager::class);
    }

    /**
     * Test for the Manager of the product attributes.
     *
     * @magentoDbIsolation disabled
     *
     * @return void
     * @throws \Zend_Db_Exception
     *
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::add()
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::_add()
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::reCreate()
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::delete()
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::has()
     * @covers \DoctorDogg\ProductsImporter\Model\ProductAttributeManager::get()
     */
    public function testAddGetDeleteCustomProductAttribute(): void
    {
        $attribute = false;
        while ($attribute === false || $attribute !== null) {
            $postfix = \mt_rand(100000, 999999);
            $attributeCode = 'product_custom_attribute_' . $postfix;
            $attributeLabel = 'Product Custom Attribute ' . $postfix;
            $attribute = $this->productAttributeManager->get($attributeCode);
        }

        $this->productAttributeManager->add($attributeCode, $attributeLabel);

        $attrExistsAfterAdding = $this->productAttributeManager->has($attributeCode);

        /**
         * Attribute exists after adding.
         */
        $this->assertTrue($attrExistsAfterAdding);

        $attribute = $this->productAttributeManager->get($attributeCode);
        /**
         * Attribute is array after adding.
         */
        $this->assertTrue(\is_array($attribute));

        /**
         * Added attribute has correct values after adding.
         */
        $this->assertSame($attributeCode, $attribute['attribute_code'] ?? '');
        $this->assertSame($attributeLabel, $attribute['frontend_label'] ?? '');

        $attributeLabelUpdated = $attributeLabel . ' re-created';
        $this->productAttributeManager->reCreate($attributeCode, $attributeLabelUpdated);
        $attributeReCreated = $this->productAttributeManager->get($attributeCode);
        /**
         * Check if recreated attribute was really recreated and exists.
         */
        $this->assertSame($attributeCode, $attributeReCreated['attribute_code'] ?? '');
        $this->assertSame($attributeLabelUpdated, $attributeReCreated['frontend_label'] ?? '');

        $this->productAttributeManager->delete($attributeCode);
        /**
         * Attribute does not exist after removing it.
         */
        $attributeDeleted = $this->productAttributeManager->get($attributeCode);

        $this->assertNull($attributeDeleted);
    }
}
