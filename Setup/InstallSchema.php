<?php
namespace PayPalBR\PayPal\Setup;


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

        $installer->endSetup();
    }
}