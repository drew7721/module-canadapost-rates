<?php
namespace JustinKase\CanadaPostRates\Model;

/**
 * Class Settings
 *
 * Class to supply all the API settings.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @deprecated Use the getConfigData method in the Carrier class instead.
 */
class Settings implements SettingsInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Settings constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getCarrierTitle()
    {
        return $this->getConfig(self::TITLE);
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->getConfig(self::CUSTOMER_NUMBER);
    }


    /**
     * Wrapper for easy access to configs.
     *
     * @param string $id
     *
     * @return string mixed
     */
    public function getConfig($id)
    {
        return $this->scopeConfig->getValue(
            sprintf(self::CONFIG_PATH, $id)
        );
    }
}
