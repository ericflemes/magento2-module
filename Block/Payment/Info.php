<?php
/**

 * @author Diego Lisboa <diego@webjump.com.br>
 * @category PayPalBR
 * @package paypalbr\PayPalPlus\
 * @copyright   qbo (http://www.webjump.com.br)
 *
 * © 2016 WEB JUMP SOLUTIONS
 *
 */

namespace qbo\PayPalPlusMx\Block\Payment;

class Info extends \Magento\Payment\Block\Info
{
    protected  $_disallowedFiledNames = array(
        'execute_url',
        'access_token',
        'handle_pending_payment'
    );
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->paymentConfig = $paymentConfig;
    }
    /**
     *
     * @param type $transport
     * @return type
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];
        $info = $this->getInfo();

        if ($this->_appState->getAreaCode() === \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE && $info->getAdditionalInformation()
        ) {
            foreach ($info->getAdditionalInformation() as $field => $value) {
                $beautifiedFieldName = str_replace("_", " ", ucwords(trim(preg_replace('/(?<=\\w)(?=[A-Z])/', " $1", $field))));
                if(!in_array($field, $this->_disallowedFiledNames)){
                    $data[__($beautifiedFieldName)->getText()] = $value;
                }
            }
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }

}
