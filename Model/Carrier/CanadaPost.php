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
     * CanadaPost constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \JustinKase\CanadaPostRates\Model\Request\Builder $requestBuilder
     * @param \JustinKase\CanadaPostRates\Model\Response\Parser $responseParser
     * @param \JustinKase\CanadaPostRates\Model\Settings $settings
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \JustinKase\CanadaPostRates\Model\Request\Builder $requestBuilder,
        \JustinKase\CanadaPostRates\Model\Response\Parser $responseParser,
        \JustinKase\CanadaPostRates\Model\Settings $settings,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->settings = $settings;
        $this->responseParser = $responseParser;
        $this->requestBuilder = $requestBuilder;
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
            $client = $this->requestBuilder->getClientForCanadaPostRateRequest();

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
}
