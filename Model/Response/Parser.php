<?php
namespace JustinKase\CanadaPostRates\Model\Response;

/**
 * Class Parser
 *
 * TODO: move strings to response interface
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class Parser implements XMLResponseInterface
{
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $methodFactory;
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $parser;
    /**
     * @var \JustinKase\CanadaPostRates\Model\Settings
     */
    protected $settings;

    /**
     * Parser constructor.
     *
     * @param \Magento\Shipping\Model\Rate\ResultFactory $resultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $methodFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Xml\Parser $parser
     * @param \JustinKase\CanadaPostRates\Model\Settings $settings
     */
    public function __construct(
        \Magento\Shipping\Model\Rate\ResultFactory $resultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $methodFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Xml\Parser $parser,
        \JustinKase\CanadaPostRates\Model\Settings $settings
    ) {
        $this->resultFactory = $resultFactory;
        $this->methodFactory = $methodFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->parser = $parser;
        $this->settings = $settings;
    }

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
        ])->getData(self::X_PATH_QUOTES);

        if (count($quotes)) {
            $carrierTitle = $this->settings->getCarrierTitle();
            foreach ($quotes as $quote) {
                if ($this->isValidQuoteRespons($quote)) {
                    $result->append(
                        $this->methodFactory->create([
                            'data' => [
                                'carrier' => \JustinKase\CanadaPostRates\Model\Carrier\CanadaPost::CODE,
                                'carrier_title' => $carrierTitle,
                                'method' => $quote[self::X_PATH_SERVICE_CODE],
                                'method_title' => $quote[self::X_PATH_SERVICE_NAME]
                            ]
                        ])->setPrice(
                            $quote[self::X_PATH_PRICE_DETAILS][self::X_PATH_DUE]
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
    private function isValidQuoteRespons($quote)
    {
        return is_array($quote)
            && isset($quote[self::X_PATH_SERVICE_CODE])
            && isset($quote[self::X_PATH_SERVICE_NAME])
            && isset($quote[self::X_PATH_PRICE_DETAILS])
            && isset($quote[self::X_PATH_PRICE_DETAILS][self::X_PATH_DUE]);
    }
}
