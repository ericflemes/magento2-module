<?php
namespace PayPalBR\PayPalPlus\Model;

use PayPalBR\PayPalPlus\Model\Config\Source\Mode;
use \Magento\Store\Model\ScopeInterface;

/**
 * Class ConfigProvider
 *
 * This class provides access to Magento 2 configuration over PayPal Plus
 *
 * @package PayPalBR\PayPalPlus\Model
 */
class ConfigProvider
{
    /**
     * Contains scope config of Magento
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Contains the client ID of PayPal Plus (Sandbox)
     */
    const XML_PATH_CLIENT_ID_SBOX = 'payment/paypal_plus/client_id_sandbox';

    /**
     * Contains the secret ID of PayPal Plus (Sandbox)
     */
    const XML_PATH_SECRET_ID_SBOX = 'payment/paypal_plus/secret_id_sandbox';

    /**
     * Contains the secret ID of PayPal Plus (Production)
     */
    const XML_PATH_CLIENT_ID_PROD= 'payment/paypal_plus/client_id_production';

    /**
     * Contains the secret ID of PayPal Plus (Production)
     */
    const XML_PATH_SECRET_ID_PROD = 'payment/paypal_plus/secret_id_production';

    /**
     * Contains the current mode, sandbox or production (live)
     */
    const XML_PATH_MODE = 'payment/paypal_plus/mode';

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Returns the current mode
     *
     * @return string
     */
    public function getMode()
    {
        $mode = $this->scopeConfig->getValue(self::XML_PATH_MODE, ScopeInterface::SCOPE_STORE);
        return $mode;
    }

    /**
     * Returns true if the mode is sandbox
     *
     * @return bool
     */
    public function isModeSandbox()
    {
        return $this->getMode() == Mode::SANDBOX;
    }

    /**
     * Returns true if the mode is production
     *
     * @return bool
     */
    public function isModeProduction()
    {
        return $this->getMode() == Mode::PRODUCTION;
    }

    /**
     * Returns the client id for sandbox
     *
     * @return string
     */
    protected function getClientIdSandbox()
    {
        $clientId = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID_SBOX, ScopeInterface::SCOPE_STORE);
        return $clientId;
    }

    /**
     * Returns the secret id for sandbox
     *
     * @return string
     */
    protected function getSecretIdSandbox()
    {
        $secretId = $this->scopeConfig->getValue(self::XML_PATH_SECRET_ID_SBOX, ScopeInterface::SCOPE_STORE);
        return $secretId;
    }

    /**
     * Returns the client id for production
     *
     * @return string
     */
    protected function getClientIdProduction()
    {
        $clientId = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID_PROD, ScopeInterface::SCOPE_STORE);
        return $clientId;
    }

    /**
     * Returns the secret id for production
     *
     * @return string
     */
    protected function getSecretIdProduction()
    {
        $secretId = $this->scopeConfig->getValue(self::XML_PATH_SECRET_ID_PROD, ScopeInterface::SCOPE_STORE);
        return $secretId;
    }

    /**
     * Returns the client ID for the current selected mode
     *
     * @return string
     * @throws \Exception
     */
    public function getClientId()
    {
        $clientId = "";
        if ($this->isModeSandbox()) {
            $clientId = $this->getClientIdSandbox();
        } else if ($this->isModeProduction()) {
            $clientId = $this->getClientIdProduction();
        } else {
            throw new \Exception("Could not determine which mode is used!");
        }
        return $clientId;
    }

    /**
     * Returns the secret ID for the current selected mode
     *
     * @return string
     * @throws \Exception
     */
    public function getSecretId()
    {
        $secretId = "";
        if ($this->isModeSandbox()) {
            $secretId = $this->getSecretIdSandbox();
        } else if ($this->isModeProduction()) {
            $secretId = $this->getSecretIdProduction();
        } else {
            throw new \Exception("Could not determine which mode is used!");
        }
        return $secretId;
    }
}