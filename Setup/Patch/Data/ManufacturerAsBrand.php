<?php

declare(strict_types=1);

namespace Kkkonrad\Brands\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Turns core's `manufacturer` into a visual swatch - the swatch image is the
 * brand logo, uploaded per option in Stores > Attributes > Product - and puts
 * it in every attribute set so it can be set on products.
 */
class ManufacturerAsBrand implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $setup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply()
    {
        $eav = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $eav->updateAttribute(Product::ENTITY, 'manufacturer', [
            'additional_data' => json_encode([
                'swatch_input_type' => 'visual',
                'update_product_preview_image' => '0',
                'use_product_image_for_swatch' => '0',
            ]),
        ]);
        foreach ($eav->getAllAttributeSetIds(Product::ENTITY) as $setId) {
            $eav->addAttributeToGroup(
                Product::ENTITY,
                $setId,
                $eav->getDefaultAttributeGroupId(Product::ENTITY, $setId),
                'manufacturer'
            );
        }
        return $this;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
