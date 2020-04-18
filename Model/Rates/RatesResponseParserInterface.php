<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Rates;

/**
 * Interface RatesResponseInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Rates
 */
interface RatesResponseParserInterface
{
    const X_PATH_QUOTES = 'price-quotes/price-quote';

    const X_PATH_SERVICE_CODE = 'service-code';

    const X_PATH_SERVICE_NAME = 'service-name';

    const X_PATH_PRICE_DETAILS = 'price-details';

    const X_PATH_DUE = 'due';

    const X_PATH_SERVICE_STANDARD = 'service-standard';

    const X_PATH_EXPECTED_DELIVERY_DATE = 'expected-delivery-date';

    /**
     * Parse the Canada Post response into a rate result.
     *
     * This will gather the data from the Canada Post XML response and create
     * the appropriate object for the response.
     *
     * @param \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier
     * @param string $xmlResponse
     *
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function getRatesFromCanadaPostXMLResponse(
        \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier,
        string $xmlResponse
    ): \Magento\Shipping\Model\Rate\Result;
}
