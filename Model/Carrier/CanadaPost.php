<?php
namespace JustinKase\CanadaPostRates\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * Class CanadaPost
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class CanadaPost extends AbstractCarrier implements CanadaPostInterface
{
    /**
     * @var string $_code
     */
    protected $_code = self::CODE;

    /**
     * @var \JustinKase\CanadaPostRates\Model\Settings
     */
    private $settings;
    /**
     * @var \JustinKase\CanadaPostRates\Model\Response\Parser
     */
    private $responseParser;
    /**
     * @var \JustinKase\CanadaPostRates\Model\Request\Builder
     */
    private $requestBuilder;
    /**
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;

    /**
     * CanadaPost constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \JustinKase\CanadaPostRates\Model\Request\Builder $requestBuilder
     * @param \JustinKase\CanadaPostRates\Model\Response\Parser $responseParser
     * @param \JustinKase\CanadaPostRates\Model\Settings $settings
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \JustinKase\CanadaPostRates\Model\Request\Builder $requestBuilder,
        \JustinKase\CanadaPostRates\Model\Response\Parser $responseParser,
        \JustinKase\CanadaPostRates\Model\Settings $settings,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->settings = $settings;
        $this->responseParser = $responseParser;
        $this->requestBuilder = $requestBuilder;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     *
     * @return \Magento\Shipping\Model\Rate\Result
     *
     * @throws \Exception
     */
    public function collectRates(RateRequest $request)
    {
        $result = false;
        // Try to build CP XML Request body from RateRequest data.
        try {
            $requestBody = $this->requestBuilder
                ->getXMLBodyForCanadaPostRateRequest($request);
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            $requestBody = false;
        }

        if ($requestBody) {
            $client = $this->getClientForCanadaPostRateRequest();

            try {
                /** @var \GuzzleHttp\Psr7\Response $response */
                $response = $client->request("POST", '', [
                    'body' => $requestBody
                ]);
            } catch (\GuzzleHttp\Exception\GuzzleException $guzzleException) {
                $this->_logger->error($guzzleException->getMessage());
            }

            try {
                /** @var  \Magento\Shipping\Model\Rate\Result $result */
                $result = $this->responseParser->getRatesFromResponseBody(
                    $response->getBody()->getContents()
                );
            } catch (\Exception $exception) {
                $this->_logger->error($exception->getMessage());
            }
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     *
     * todo: use this to show or not the rates based.
     */
    public function getAllowedMethods()
    {
        /** @var array $allowedMethods */
        $configMethods =  $this->getConfigData('allowed_methods');
        $allowedMethods = explode(',', $configMethods);
        return $allowedMethods;
    }

    /**
     * Get the rates request Guzzle client.
     *
     * //TODO: abstract this in a client provider class extended for the CP apis.
     * @return \GuzzleHttp\Client
     */
    public function getClientForCanadaPostRateRequest()
    {
        return (new \GuzzleHttp\Client([
            'base_uri' => $this->getRatesEndpoint(),
            'auth' => $this->getAuth(),
            'headers' => $this->getRequestHeaders(),
        ]));
    }

    /**
     * Returns the proper endpoint based on the set request mode.
     *
     * Developer || Production
     *
     * @return string
     */
    private function getRatesEndpoint()
    {
        $domain = self::API_ENDPOINTS[$this->getConfigData(self::REQUEST_MODE)];
        return sprintf('https://%s/rs/ship/price', $domain);
    }

    /**
     * Get auth headers for Guzzle.
     *
     * @see http://docs.guzzlephp.org/en/stable/request-options.html#auth
     *
     * @return array
     */
    private function getAuth()
    {
        return [
            $this->getConfigData(self::USERNAME),
            $this->getConfigData(self::PASSWORD)
        ];
    }

    /**
     * Get minimal CP headers for the API request.
     *
     * @return array
     */
    public function getRequestHeaders()
    {
        return [
            'Accept' => RatesClientInterface::RATES_HEADER_CONTENT_TYPE,
            'Content-Type' => RatesClientInterface::RATES_HEADER_CONTENT_TYPE,
            'Accept-language' => $this->resolveLocale()
        ];
    }

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
    private function resolveLocale()
    {
        $locale = $this->localeResolver->getLocale();
        foreach (self::HEADER_ACCEPTED_LANGUAGE_MAP as $code => $haystack) {
            if (strpos($haystack, $locale) !== false) {
                return $code;
            }
        }
        return array_key_first(self::HEADER_ACCEPTED_LANGUAGE_MAP);
    }
}
