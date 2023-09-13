<?php

declare(strict_types=1);

namespace DoctorDogg\ProductsImporter\Model;

use \Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Downloadable\Model\Product\Type;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Validator\ValidateException;
use Magento\GroupedProduct\Model\Product\Type\Grouped;
use \DoctorDogg\ProductsImporter\Api\ProductAttributeAdminSettingInterface;

/**
 * Class adds the custom attribute to the product entity.
 */
class ProductAttributeManager
{
    /**
     * @var EavSetup $eavSetup
     */
    private EavSetup $eavSetup;

    /**
     * @var EavSetupFactory $eavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var CategorySetupFactory
     */
    private CategorySetupFactory $categorySetupFactory;

    /**
     * Constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Add product attribute to the product.
     *
     * @param string $attributeCode
     * @param string $label
     * @return void
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function add(string $attributeCode, string $label): void
    {
        /**
         * @var CategorySetup $categorySetup
         */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Previously we checked that attribute is not presented in the EAV of the Product,
         * and we should add it to the product.
         *
         * So we can have two strategies:
         *  - Remove the attribute and add again
         *  - Do nothing if attribute exists
         */
        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attribute = $categorySetup->getAttribute($entityTypeId, $attributeCode);

        /**
         * First strategy:
         * - Remove the attribute and add again
         */
        /**
         * It's a bad approach to remove an attribute, because if the product already has this data,
         * it will obviously lead to their loss most likely.
         *
         * @move to method reCreate(string $attributeCode, string $label)
         * if ($attribute) {
         *     $this->delete($attributeCode);
         *     $this->_add($attributeCode, $label);
         * } else {
         *     $this->_add($attributeCode, $label);
         * }
         */

        /**
         * Second strategy:
         * - Do nothing if attribute exists
         */
        if ($attribute) {
            // just do nothing.
        } else {
            $this->_add($attributeCode, $label);
        }
    }

    /**
     * Re create product attribute.
     *
     * @param string $attributeCode
     * @param string $label
     * @return void
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function reCreate(string $attributeCode, string $label): void
    {
        /**
         * @var CategorySetup $categorySetup
         */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        /**
         * Previously we checked if attribute is presented in the EAV of the Product,
         * and then we should remove it and add it again back to the product.
         */
        $entityTypeId = $categorySetup->getEntityTypeId(Product::ENTITY);
        $attribute = $categorySetup->getAttribute($entityTypeId, $attributeCode);

        /**
         * First strategy:
         * - Remove the attribute and add again
         */
        if ($attribute) {
            $this->delete($attributeCode);
            $this->_add($attributeCode, $label);
        } else {
            $this->_add($attributeCode, $label);
        }
    }

    /**
     * Just internal method for adding attribute.
     *
     * @param string $attributeCode
     * @param string $label
     * @return void
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function _add(string $attributeCode, string $label): void
    {
        $productTypes = [
            ProductType::TYPE_SIMPLE,
            Configurable::TYPE_CODE,
            ProductType::TYPE_VIRTUAL,
            Type::TYPE_DOWNLOADABLE,
            ProductType::TYPE_BUNDLE,
            Grouped::TYPE_CODE
        ];
        $productTypes = \join(',', $productTypes);
        $this->eavSetup->addAttribute(
            Product::ENTITY,
            $attributeCode,
            [
                'group' => ProductAttributeAdminSettingInterface::GROUP_NAME,
                'backend' => '',
                'frontend' => '',
                'label' => $label,
                'type' => 'varchar',
                'input' => 'text',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true,
                'comparable' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'apply_to' => $productTypes,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
            ]
        );
    }

    /**
     * Delete product attribute from the product.
     *
     * @param string $attributeCode
     * @return void
     */
    public function delete(string $attributeCode): void
    {
        $this->eavSetup->removeAttribute(Product::ENTITY, $attributeCode);
    }

    /**
     * Check if the attribute code exists in product.
     *
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     */
    public function has(string $attributeCode): bool
    {
        return $this->get($attributeCode) !== null;
    }

    /**
     * Get product attribute by its code.
     *
     * @param string $attributeCode
     * @return array|null
     * @throws LocalizedException
     */
    public function get(string $attributeCode)
    {
        $entityTypeId = $this->eavSetup->getEntityTypeId(Product::ENTITY);
        $productAttribute = $this->eavSetup->getAttribute($entityTypeId, $attributeCode);
        if (!\is_array($productAttribute) ||
            !\count($productAttribute)) {
            return null;
        }
        return $productAttribute;
    }
}
