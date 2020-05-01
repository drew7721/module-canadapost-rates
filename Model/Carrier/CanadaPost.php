<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

use JustinKase\CanadaPostRates\Model\Rates\RequestBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline as Carrier;
use JustinKase\CanadaPostRates\Api\GlobalConfigs;
use JustinKase\CanadaPostRates\Model\Rates\RatesBuilderInterface;

/**
 * Class CanadaPost
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Carrier
 */
class CanadaPost extends Carrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const CODE = 'canadapost';
    /**
     * @var string $_code
     */
    protected $_code = 'canadapost';

    protected $allowedMethods = null;


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
     * @var \JustinKase\CanadaPostRates\Model\Rates\RatesResponseParserInterface ratesResponseParser
     */
    private $ratesResponseParser;
    /**
     * @var \JustinKase\CanadaPostRates\Model\Rates\RequestBuilder requestBuilder
     */
    private $requestBuilder;

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
     * @param \JustinKase\CanadaPostRates\Model\Rates\RatesResponseParserInterface $ratesResponseParser
     * @param \JustinKase\CanadaPostRates\Api\ClientInterface $client
     * @param \JustinKase\CanadaPostRates\Model\Rates\RequestBuilder $requestBuilder
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
        \JustinKase\CanadaPostRates\Model\Rates\RatesResponseParserInterface $ratesResponseParser,
        \JustinKase\CanadaPostRates\Api\ClientInterface $client,
        RequestBuilder $requestBuilder,
        array $data = []
    ) {
        $this->client = $client;
        $this->ratesResponseParser = $ratesResponseParser;
        $this->requestBuilder = $requestBuilder;

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
        try {
            $requestBody = $this->requestBuilder->build($request);
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
                $result = $this->ratesResponseParser->getRatesFromCanadaPostXMLResponse(
                    $this,
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
        return $this->createShipmentRequest($request);
    }

    /**
     * Create shipping request with Canada Post.
     *
     * This public method allows external implementation of the shipment
     * creation.
     *
     * @param \Magento\Framework\DataObject $request
     */
    public function createShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $notAvailable = "Add-on module required.";
        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA);

        $pdf = (new \Zend_Pdf());

        $pdf->pages[0] = (new \Zend_Pdf_Page(\Zend_Pdf_Page::SIZE_A4));
        $pdf->pages[0]->setFont($font, 12);

        $pdf->pages[0]->drawText(
            __("Automatic shipment creation unavailable. You need to install a shipment add-on module. Visit justinkase.ca for more information."),
            10,
            $pdf->pages[0]->getHeight() - 20
        );

        return ObjectManager::getInstance()->create(
            \Magento\Framework\DataObject::class,
            [
                'data' => [
                    'tracking_number' => $notAvailable,
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
}
