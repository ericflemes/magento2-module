<?php
namespace PayPalBR\PayPalPlus\Setup;


use \Magento\Framework\Setup\ModuleContextInterface as ModuleContextInterface;
use \Magento\Framework\Setup\SchemaSetupInterface as SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * Instala as tabelas.
     *
     * @since  2017-07-18
     *
     * @param  MagentoFrameworkSetupSchemaSetupInterface    $setup      Instância do instalador
     * @param  MagentoFrameworkSetupModuleContextInterface  $context    Instância do contexto
     * @return null
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Alter table 'quote'
         */
        $installer->getConnection()->addColumn(
            $installer->getTable('customer_eav_attribute'),
            'remembered_card',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'label' => 'PayPalPlus Remembered Card',
                'input' => 'text',
                'backend' => 'PayPalBR\PayPalPlus\Model\Customer\Token'
            ]
        );

        $installer->endSetup();
    }
}