<?php

namespace PayPalBR\PayPalPlus\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $eavConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory, 
        Config $eavConfig
    ) {
        $this->setEavSetupFactory($eavSetupFactory);
        $this->setEavConfig($eavConfig);
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->getEavSetupFactory()->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            Customer::ENTITY,
            'customer_token',
            [
                'label'         => 'Token Access PayPal',
                'type'          => 'static',
                'input'         => 'text',
                'required'      => false,
                'default'       => '',

            ]
        );
        $attribute = $this->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_token');
        $attribute->setData(
            'used_in_forms',
            ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
        );
        $attribute->save();
    }

    /**
     * @return mixed
     */
    public function getEavSetupFactory()
    {
        return $this->eavSetupFactory;
    }

    /**
     * @param mixed $eavSetupFactory
     *
     * @return self
     */
    public function setEavSetupFactory($eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEavConfig()
    {
        return $this->eavConfig;
    }

    /**
     * @param mixed $eavConfig
     *
     * @return self
     */
    public function setEavConfig($eavConfig)
    {
        $this->eavConfig = $eavConfig;

        return $this;
    }
}
