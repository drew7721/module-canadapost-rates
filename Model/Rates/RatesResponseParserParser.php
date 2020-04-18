<?php

/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Rates;

use JustinKase\CanadaPostRates\Api\GlobalConfigs;
use Magento\Shipping\Model\Rate\Result;

/**
 * Class ResponseParser
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Rates
 */
class RatesResponseParserParser implements RatesResponseParserInterface
{
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory rateResultFactory
     */
    protected $rateResultFactory;
    /**
     * @var \Magento\Framework\Xml\Parser xmlParser
     */
    private $xmlParser;
    /**
     * @var \Magento\Framework\DataObjectFactory dataObjectFactory
     */
    private $dataObjectFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory rateMethodFactory
     */
    private $rateMethodFactory;

    /**
     * ResponseParser constructor.
     *
     * @param \Magento\Framework\Xml\Parser $xmlParser
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        \Magento\Framework\Xml\Parser $xmlParser,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    ) {
        $this->xmlParser = $xmlParser;
        $this->rateResultFactory = $rateResultFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->rateMethodFactory = $rateMethodFactory;
    }

    /**
     * @inheritDoc
     */
    public function getRatesFromCanadaPostXMLResponse(
        \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier,
        string $xmlResponse
    ): Result {
        $rateResult = $this->rateResultFactory->create();
        $data = '';
        try {
            $data = $this->xmlParser->loadXML($xmlResponse)->xmlToArray();
        } catch (\Magento\Framework\Exception\LocalizedException $localizedException) {
            $rateResult->setError(true);
            return $rateResult;
        }

        /** @var array $quotes */
        $quotes = $this->dataObjectFactory->create(
            [
                'data' => $data
            ]
        )->getData(self::X_PATH_QUOTES);

        if (count($quotes)) {
            $carrierTitle = $carrier->getConfigData(GlobalConfigs::GLOBAL_CARRIER_TITLE);
            foreach ($quotes as $quote) {
                if ($this->isValidQuoteResponse($quote, $carrier)) {
                    $rateResult->append(
                        $this->rateMethodFactory->create(
                            [
                                'data' => [
                                  'carrier' => GlobalConfigs::CARRIER_CODE,
                                  'carrier_title' => $carrierTitle,
                                  'method' => $quote[self::X_PATH_SERVICE_CODE],
                                  'method_title' => $quote[self::X_PATH_SERVICE_NAME]
                                ]
                            ]
                        )->setPrice(
                            $quote[self::X_PATH_PRICE_DETAILS][self::X_PATH_DUE]
                        )
                    );
                }
            }
        } else {
            $rateResult->setError(true);
        }

        return $rateResult;
    }

    /**
     * Check if the response rate quote.
     *
     * Check if the response quote has the mandatory data.
     *
     * @param array $quote
     *
     * @param \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier
     *
     * @return bool
     */
    private function isValidQuoteResponse($quote, $carrier)
    {
        $allowedMethods = $carrier->getAllowedMethods();
        $isAllowed = in_array($quote[self::X_PATH_SERVICE_CODE], $allowedMethods);
        return is_array($quote)
            && isset($quote[self::X_PATH_SERVICE_CODE])
            && $isAllowed
            && isset($quote[self::X_PATH_SERVICE_NAME])
            && isset($quote[self::X_PATH_PRICE_DETAILS])
            && isset($quote[self::X_PATH_PRICE_DETAILS][self::X_PATH_DUE]);
    }
}
