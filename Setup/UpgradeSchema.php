<?php
namespace PayPalBR\PayPalPlus\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    public function __construct(CustomerSetupFactory $customerSetupFactory)
    {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $dbVersion = $context->getVersion();

        if (version_compare($dbVersion, '0.0.13', '<')) {

            /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
            $customerSetup->addAttribute(
            'customer_token',
                [
                    'label'         => 'Token Access PayPal',
                    'type'          => 'static',
                    'input'         => 'text',
                    'required'      => false,
                    'default'       => '',

                ]
            );
            $customerSetup->getEavConfig()->getAttribute('customer', 'customer_token')
                ->setData('used_in_forms', ['adminhtml_customer'])
                ->save();
        }
    }
}