<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline as Carrier;
use JustinKase\CanadaPostRates\Api\GlobalConfigs;

/**
 * Class CanadaPost
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class CanadaPost extends Carrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const CODE = '';
    /**
     * @var string $_code
     */
    protected $_code = 'canadapost';

    protected $allowedMethods = null;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $parser;

    ////
    /// RATES MANDATORY VALUES
    ///

    /**
     * Weight in Kg. Default 10Kg.
     * @var float $weight
     */
    protected $weight = 10.0;

    protected $postalCode = null;

    protected $countryCode = null;

    protected $destinationTag = RatesBuilderInterface::DESTINATION_INTERNATIONAL;

    protected $postalCodeTag = RatesBuilderInterface::POSTAL_CODE_TAG;

    /**
     * @var \JustinKase\CanadaPostRates\Api\ClientInterface client
     */
    private $client;

    /**
     * CanadaPost constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Xml\Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Xml\Parser $parser
     * @param \JustinKase\CanadaPostRates\Api\ClientInterface $client
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Xml\Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Xml\Parser $parser,
        \JustinKase\CanadaPostRates\Api\ClientInterface $client,
        array $data = []
    ) {
        $this->dataObjectFactory = $dataObjectFactory;
        $this->parser = $parser;
        $this->client = $client;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
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
            try {
                /** @var \GuzzleHttp\Psr7\Response $response */
                $response = $this->client->requestCanadaPostApi([
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
     */
    public function getAllowedMethods()
    {
        if ($this->allowedMethods === null) {
            $this->allowedMethods = explode(',', $this->getConfigData(
                GlobalConfigs::GLOBAL_ALLOWED_METHODS
            ));
        }

        return $this->allowedMethods ?: [];
    }
    ////
    /// ONLINE CARRIER METHODS
    ///

    /**
     * @inheritDoc
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $randomTracking = "not_available_in_free_version";
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);

        $pdf = (new \Zend_Pdf());

        $pdf->pages[0] = (new \Zend_Pdf_Page(\Zend_Pdf_Page::SIZE_A4));
        $pdf->pages[0]->setFont($font, 12);

        $pdf->pages[0]->drawText(
            __("Buy shipping module to create labels automatically."),
            10,
            $pdf->pages[0]->getHeight() - 20
        );
        return ObjectManager::getInstance()->create(
            \Magento\Framework\DataObject::class,
            [
                'data' => [
                    'tracking_number' => $randomTracking,
                    'shipping_label_content' => $pdf->render()
                ]
            ]
        );
    }

    //// TRACKING METHODS

    /**
     * Get tracking for packages.
     *
     * @param string|array $tracking
     *
     * @return \Magento\Shipping\Model\Tracking\Result
     */
    public function getTracking($tracking)
    {
        /** @var \Magento\Shipping\Model\Tracking\Result $result */
        $result = ObjectManager::getInstance()->create(
            \Magento\Shipping\Model\Tracking\Result::class,
            []
        );

        if (is_array($tracking)) {
            foreach ($tracking as $number) {
                $result->append($this->getTrackingDetailsByNumber($number));
            }
        } else {
            $result->append($this->getTrackingDetailsByNumber($tracking));
        }

        return $result;
    }

    /**
     * Get Canada post information on package.
     *
     * @param string $trackingNumber
     *
     * @return \Magento\Shipping\Model\Tracking\Result\Status
     */
    public function getTrackingDetailsByNumber($trackingNumber)
    {
        return ObjectManager::getInstance()->create(
            \Magento\Shipping\Model\Tracking\Result\Status::class,
            [
                'data' => [
                    'tracking' => $trackingNumber,
                    'carrier_title' => $this->getConfigData(GlobalConfigs::GLOBAL_CARRIER_TITLE),
                    //else
                    'url' => "https://www.canadapost.ca/trackweb/en#/details/{$trackingNumber}", //basic
                    'status' => 'Buy our tracking module to have detailed tracking information for each package.',
                ]
            ]
        );
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
        $result = $this->_rateFactory->create();

        /** @var array $quotes */
        $quotes = $this->dataObjectFactory->create([
            'data' => $this->parser->loadXML($xmlResponse)->xmlToArray()
        ])->getData(RatesResponseInterface::X_PATH_QUOTES);

        if (count($quotes)) {
            $carrierTitle = $this->getConfigData(GlobalConfigs::GLOBAL_CARRIER_TITLE);
            foreach ($quotes as $quote) {
                if ($this->isValidQuoteResponse($quote)) {
                    $result->append(
                        $this->_rateMethodFactory->create([
                            'data' => [
                                'carrier' => GlobalConfigs::CARRIER_CODE,
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

        $customerNumber = $this->getConfigData(GlobalConfigs::GLOBAL_CUSTOMER_NUMBER);
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
