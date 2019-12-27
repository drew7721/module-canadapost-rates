<?php
namespace JustinKase\CanadaPostRates\Model;

/**
 * Class Settings
 *
 * Class to supply all the API settings.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class Settings implements SettingsInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * Settings constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Locale\Resolver $localeResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @return string
     */
    public function isCanadaPostCarrierActive()
    {
        return $this->getConfig(self::ACTIVE);
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
    public function getUsername()
    {
        return $this->getConfig(self::USERNAME);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getConfig(self::PASSWORD);
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->getConfig(self::CUSTOMER_NUMBER);
    }

    /**
     * @return string
     */
    public function getContractId()
    {
        return $this->getConfig(self::CONTRACT_ID);
    }

    /**
     * @return string
     */
    public function getSpecificErrorMessage()
    {
        return $this->getConfig(self::SPECIFIC_ERROR_MESSAGE);
    }

    /**
     * @return string
     */
    public function getOriginPostalCode()
    {
        return $this->scopeConfig->getValue(self::ORIGIN_POSTAL_CODE);
    }

    /**
     * Returns the proper endpoint based on the set request mode.
     *
     * Developer || Production
     *
     * @return string
     */
    public function getEndpoint()
    {
        $domain = self::API_ENDPOINTS[$this->getConfig(self::REQUEST_MODE)];
        return sprintf('https://%s/rs/ship/price', $domain);
    }

    /**
     * Get minimal CP headers for the API request.
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        return [
            'Accept' => self::HEADER_CONTENT_TYPE,
            'Content-Type' => self::HEADER_CONTENT_TYPE,
            'Accept-language' => $this->resolveLocale()
        ];
    }

    private function resolveLocale()
    {
        $locale = $this->localeResolver->getLocale();
        foreach (self::HEADER_ACCEPTED_LANGUAGE_MAP as $code => $haystack) {
            if (strpos($haystack, $locale) !== false) {
                return $code;
            }
        }
        return self::DEFAULT_LANGUAGE_CODE;
    }

    /**
     * Get auth headers for Guzzle.
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#auth
     *
     * @return array
     */
    public function getAuth()
    {
        return [
            $this->getUsername(),
            $this->getPassword()
        ];
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
