<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Client;

/**
 * Class Client
 *
 * This is the base client to call the Canada Post API.
 *
 * It uses Guzzle Http to connect. This client will be used for all the calls
 * to the API.
 *
 * As multiple endpoints need to be called during different transactions this
 * should not implement the endpoint it will use. Instead, the endpoint will be
 * provided by the call.
 *
 * However, all calls need to be authenticated. This will maintain a global
 * authentication as well as consider the proper endpoint based on the current
 * setting between production and development.
 *
 * @NOTE There is no default config for this client. You need to supply one from
 * the di.xml file to change the API endpoint details.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Client
 */
class Client extends \GuzzleHttp\Client implements \JustinKase\CanadaPostRates\Api\ClientInterface
{
    /**
     * @var \Magento\Framework\Locale\Resolver localeResolver
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface scopeConfig
     */
    protected $scopeConfig;
    /**
     * @var \JustinKase\CanadaPostRates\Api\ClientConfigInterface clientConfig
     */
    private $clientConfig;

    /**
     * Client constructor.
     *
     * @param \JustinKase\CanadaPostRates\Api\ClientConfigInterface $clientConfig
     * @param array $config
     */
    public function __construct(
        \JustinKase\CanadaPostRates\Api\ClientConfigInterface $clientConfig,
        array $config = []
    ) {
        $this->clientConfig = $clientConfig;
        parent::__construct($config + $this->getDefaultConfig());
    }

    /**
     * Get the default configs from the ClientConfig provider.
     * @return array
     */
    private function getDefaultConfig()
    {
        return [
            'auth' => $this->clientConfig->getAuthorizationArray(),
            'headers' => $this->clientConfig->getRequestHeaders(),
        ];
    }

    /**
     * Place request with Canada Post API
     *
     * This uses the set config to request the data from the API.
     *
     * It's the callers responsibility to pass valid data and to treat the
     * response.
     *
     * @param array $options
     *
     * @param null|string $uri
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function requestCanadaPostApi(array $options = [], $uri = null): \Psr\Http\Message\ResponseInterface
    {
        return parent::request(
            $this->clientConfig->getRequestMethod(),
            $uri ?? $this->clientConfig->getRequestUri(),
            $options
        );
    }
}
