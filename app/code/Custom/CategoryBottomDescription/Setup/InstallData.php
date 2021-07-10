<?php

namespace Custom\CategoryBottomDescription\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            'category_bottom_description',
            [
                'type'         => 'text',
                'label'        => 'Category Bottom Description',
                'input'        => 'textarea',
                'required'     => false,
                'sort_order'   => 100,
                'visible'      => true,
                'source'       => '',
                'global'       => ScopedAttributeInterface::SCOPE_STORE,
                'group'        => 'Content',
                'user_defined' => false,
                'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
            ]
        );
        $setup->endSetup();
    }
}
