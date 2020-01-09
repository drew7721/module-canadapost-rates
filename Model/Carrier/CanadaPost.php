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
     * @var \Magento\Framework\Locale\Resolver
     */
    private $localeResolver;
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $resultFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $methodFactory;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $parser;

    ////
    /// RATES MANDATORY VALUES
    ///

    /**
     * Weight in Kg. Default 10Kg.
     * @var float $weight
     */
    private $weight = 10.0;

    private $postalCode = null;

    private $countryCode = null;

    private $destinationTag = RatesBuilderInterface::DESTINATION_INTERNATIONAL;

    private $postalCodeTag = RatesBuilderInterface::POSTAL_CODE_TAG;

    /**
     * CanadaPost constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $resultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $methodFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Xml\Parser $parser
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $resultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $methodFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Xml\Parser $parser,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->localeResolver = $localeResolver;
        $this->resultFactory = $resultFactory;
        $this->methodFactory = $methodFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->parser = $parser;
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
            $requestBody = $this->getXMLBodyForCanadaPostRateRequest($request);
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
                $result = $this->getRatesFromResponseBody(
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

    ////
    /// CLIENT METHODS
    ////
    /**
     * Get the rates request Guzzle client.
     *
     * //TODO: abstract this in a client provider class extended for the CP apis.
     * @return \GuzzleHttp\Client
     */
    private function getClientForCanadaPostRateRequest()
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
    private function getRequestHeaders()
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

    ////
    /// RATES RESPONSE PARSING METHODS
    ///

    /**
     * Parse the response body and create rate results.
     *
     * This will parse the XML body from the Canada Post response and will
     * generate a valid result with all the valid methods.
     *
     * @param string $xmlResponse
     *
     * @return \Magento\Shipping\Model\Rate\Result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRatesFromResponseBody($xmlResponse)
    {
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->resultFactory->create();

        /** @var array $quotes */
        $quotes = $this->dataObjectFactory->create([
            'data' => $this->parser->loadXML($xmlResponse)->xmlToArray()
        ])->getData(RatesResponseInterface::X_PATH_QUOTES);

        if (count($quotes)) {
            $carrierTitle = $this->getConfigData(self::TITLE);
            foreach ($quotes as $quote) {
                if ($this->isValidQuoteResponse($quote)) {
                    $result->append(
                        $this->methodFactory->create([
                            'data' => [
                                'carrier' => \JustinKase\CanadaPostRates\Model\Carrier\CanadaPost::CODE,
                                'carrier_title' => $carrierTitle,
                                'method' => $quote[RatesResponseInterface::X_PATH_SERVICE_CODE],
                                'method_title' => $quote[RatesResponseInterface::X_PATH_SERVICE_NAME]
                            ]
                        ])->setPrice(
                            $quote[RatesResponseInterface::X_PATH_PRICE_DETAILS][RatesResponseInterface::X_PATH_DUE]
                        )
                    );
                }
            }
        } else {
            $result->setError(true);
        }

        return $result;
    }

    /**
     * Check if the response rate quote.
     *
     * Check if the response quote has the mandatory data.
     *
     * @param $quote
     *
     * @return bool
     */
    private function isValidQuoteResponse($quote)
    {
        $allowedMethods = $this->getAllowedMethods();
        $isAllowed = in_array($quote[RatesResponseInterface::X_PATH_SERVICE_CODE], $allowedMethods);
        return is_array($quote)
            && isset($quote[RatesResponseInterface::X_PATH_SERVICE_CODE])
            && $isAllowed
            && isset($quote[RatesResponseInterface::X_PATH_SERVICE_NAME])
            && isset($quote[RatesResponseInterface::X_PATH_PRICE_DETAILS])
            && isset($quote[RatesResponseInterface::X_PATH_PRICE_DETAILS][RatesResponseInterface::X_PATH_DUE]);
    }


    ////
    /// XML DOCUMENT BUILDER METHODS
    ///
    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return bool|string
     * @throws \Exception
     */
    public function getXMLBodyForCanadaPostRateRequest(
        \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
    ) {
        $result = false;

        if ($this->isValidRateRequest($rateRequest)) {
            $result = $this->buildXmlRequest($rateRequest);
        }

        return $result;
    }

    /**
     * @param null $weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        if (!empty($weight)) {
            $this->weight = sprintf('%2.3F', $weight);
        }

        return $this;
    }

    /**
     * Set the postal code.
     *
     * API requires this to be uppercase and no special chars.
     *
     * @param string $postalCode
     *
     * @return $this
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = preg_replace(
            '/\W/',
            '',
            strtoupper($postalCode)
        );

        return $this;
    }

    /**
     * Set the country code.
     *
     * This needs to be a valid 2 uppercase letters country code.
     *
     * The request is build differently based on this.
     *
     * US => united-states
     * CA => domestic
     * OTHER => international
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = preg_replace(
            '/(\W|\d)/',
            '',
            strtoupper($countryCode)
        );

        return $this;
    }

    /**
     * Todo: move this to the carrier validation method.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return bool
     * @deprecated  This will be done prior to even building the Carrier.
     */
    private function isValidRateRequest(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        $destinationCountryCode = $rateRequest->getDestCountryId();
        if (empty($destinationCountryCode)) {
            return false;
        } else {
            $this->setCountryCode($destinationCountryCode);
            if (array_key_exists($this->countryCode, RatesBuilderInterface::COUNTRY_CODES)) {
                $this->destinationTag = RatesBuilderInterface::COUNTRY_CODES[$this->countryCode];
            }

            $valid = preg_match(RatesBuilderInterface::COUNTRY_CODE_MATCH_PATTERN, $this->postalCode);
            if ($valid === false) {
                return false;
            }

            if ($this->destinationTag === RatesBuilderInterface::DESTINATION_USA) {
                $this->postalCodeTag = RatesBuilderInterface::POSTAL_CODE_US_TAG;
            }
        }

        $destinationPostalCode = $rateRequest->getDestPostcode();
        if (empty($destinationPostalCode) && $this->destinationTag !== RatesBuilderInterface::DESTINATION_INTERNATIONAL) {
            return false;
        } else {
            $this->setPostalCode($destinationPostalCode);

            if ($this->destinationTag === RatesBuilderInterface::DESTINATION_DOMESTIC) {
                $valid = preg_match(RatesBuilderInterface::POSTAL_COTE_MATCH_PATTERN_CANADA, $this->postalCode);
                if ($valid === false) {
                    return false;
                }
            }
        }

        $this->setWeight($rateRequest->getPackageWeight());

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return string
     * @throws \Exception
     */
    private function buildXmlRequest(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        $xmlDocument = new \DOMDocument('1.0', 'UTF-8');
        $scenario = $xmlDocument->createElement(RatesBuilderInterface::MAILING_SCENARIO_TAG);

        $scenario->setAttribute(
            'xmlns',
            RatesBuilderInterface::XMLNS_VALUE
        );

        $xmlDocument->appendChild($scenario);

        $customerNumber = $this->getConfigData(self::CUSTOMER_NUMBER);
        if (!empty($customerNumber)) {
            $scenario->appendChild(
                $xmlDocument->createElement('customer-number', $customerNumber)
            );
        }

        $originalPostalCode = $rateRequest->getOrigPostcode() ?: $rateRequest->getPostcode();

        $scenario->appendChild(
            $xmlDocument->createElement(
                'origin-postal-code',
                $originalPostalCode
            )
        );

        $parcel = $xmlDocument->createElement('parcel-characteristics');

        $parcel->appendChild(
            $xmlDocument->createElement('weight', $this->weight)
        );
        $scenario->appendChild($parcel);

        $destination = $xmlDocument->createElement('destination');
        $destinationType = $xmlDocument->createElement($this->destinationTag);
        $destination->appendChild($destinationType);

        $destinationPostalCode = $xmlDocument->createElement(
            $this->postalCodeTag,
            $this->postalCode
        );
        $destinationType->appendChild($destinationPostalCode);

        if ($this->destinationTag === RatesBuilderInterface::DESTINATION_INTERNATIONAL) {
            $destinationCountryCode = $xmlDocument->createElement(
                'country-code',
                $this->countryCode
            );
            $destinationType->appendChild($destinationCountryCode);
        }

        $scenario->appendChild($destination);

        return $xmlDocument->saveXML();
    }
}
