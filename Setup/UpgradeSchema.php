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
    public function upgrade(
        ModuleDataSetupInterface $setup, 
        ModuleContextInterface $context
    ){
        $dbVersion = $context->getVersion();

        if (version_compare($context->getVersion(), "0.2.10", "<")) {
            $setup = $this->updateVersionZeroTwoTen($setup);
        }

        if (version_compare($dbVersion, '0.3.3', '<')) {
            $setup = $this->updateVersionZeroTreeTree($setup);
            
        }
    }

    protected function updateVersionZeroTwoTen($setup)
    {
        $tableName = $setup->getTable('sales_order_status_state');

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $connection = $setup->getConnection();
            $where = ['state = ?' => 'pending_payment'];
            $data = ['visible_on_front' => 1];
            $connection->update($tableName, $data, $where);
        }

        return $setup;
    }

    protected function updateVersionZeroTreeTree($setup)
    {
        $setup->startSetup();

        $tableName = $setup->getTable('sales_order_status_state');
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributeCode = 'remembered_card';
        $customerSetup->removeAttribute(\Magento\Customer\Model\Customer::ENTITY, $attributeCode);
        $customerSetup->addAttribute(
            'customer',
            'remembered_card', 
            [
                'label' => 'Remembered Card',
                'type' => 'text',
                'frontend_input' => 'text',
                'required' => false,
                'visible' => false,
                'system'=> true,
                'position' => 105,
                'sort_order' => 100,
                'user_defined' => true,
            ]
        );

        $eavConfig = $customerSetup->getEavConfig()->getAttribute('customer', 'remembered_card');
        $eavConfig->setData('used_in_forms',['adminhtml_customer']);
        $eavConfig->addData([
            'attribute_set_id' => 1,
            'attribute_group_id' => 1
        ]);
        $eavConfig->save();
        $setup->endSetup();

        return $setup;
    }
}