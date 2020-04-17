<?php

/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model;

use JustinKase\CanadaPostRates\Api\ClientConfig;
use Magento\Tests\NamingConvention\true\string;

/**
 * Class AbstractClientConfig
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model
 */
abstract class AbstractClientConfig implements ClientConfig
{
    /**
     * The configuration that will be returned.
     *
     * @var array config
     */
    protected $config = [];

    /**
     * @var \Magento\Framework\Locale\Resolver localeResolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface scopeConfig
     */
    protected $scopeConfig;

    /**
     * AbstractClientConfig constructor.
     *
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->localeResolver = $localeResolver;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Implement this to get the suffix of the API call URI.
     *
     * @return string
     */
    abstract public function getUriSuffix(): string;

    /**
     * This will need to be implemented at a request config level.
     *
     * @return array
     */
    abstract public function getRequestHeaders(): array;

    /**
     * Implement to return relevant request method. (POST|GET)
     *
     * @return string
     */
    abstract public function getRequestMethod(): string;

    /**
     * Return the current locale.
     *
     * Check the current locale that needs to be passed in the header to the
     * request so that Canada Post returns the names in the correct language.
     *
     * Choices are FR or EN and current locales have been mapped.
     *
     * @return string
     */
    public function resolveLocale(): string
    {
        $locale = $this->localeResolver->getLocale();
        foreach (self::HEADER_ACCEPTED_LANGUAGE_MAP as $code => $haystack) {
            if (strpos($haystack, $locale) !== false) {
                return $code;
            }
        }
        return array_key_first(self::HEADER_ACCEPTED_LANGUAGE_MAP);
    }

    /**
     * Get the base of the current request
     *
     * @return string
     */
    public function getRequestUri(): string
    {
        $baseUri = sprintf('https://%s/', $this->getHost(
            $this->getCanadaPostConfig('request_mode')
        ));

        return $baseUri . $this->getUriSuffix();
    }

    /**
     * Get the headers authorisation array.
     *
     * This will be used to authenticate with Canada Post.
     *
     * @return array
     * @throws \JustinKase\CanadaPostRates\Model\CanadaPostException
     */
    public function getAuthorizationArray(): array
    {
        $apiUsername = $this->getCanadaPostConfig('username');
        $apiPassword = $this->getCanadaPostConfig('password');

        if (empty($apiPassword) || empty($apiUsername)) {
            throw new CanadaPostException(
                "You have to configure your Canada Post API credentials."
            );
        }

        return [
            $apiUsername,
            $apiPassword
        ];
    }

    /**
     * Method to get carrier specific config by field name.
     *
     * @param $field
     *
     * @return string
     */
    protected function getCanadaPostConfig($field): string
    {
        return $this->scopeConfig->getValue("carriers/canadapost/{$field}");
    }
}
