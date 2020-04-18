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
 * Interface RatesBuilderInterface
 *
 * Contains the constants needed to build the XML request structure that will
 * be sent to the Canada Post API.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Rates
 */
interface RatesBuilderInterface
{
    const XMLNS_VALUE = 'http://www.canadapost.ca/ws/ship/rate-v4';

    const DESTINATION_DOMESTIC = 'domestic';

    const DESTINATION_USA = 'united-states';

    const DESTINATION_INTERNATIONAL = 'international';

    const COUNTRY_CODES = [
        'CA' => self::DESTINATION_DOMESTIC,
        'US' => self::DESTINATION_USA
    ];

    const POSTAL_CODE_TAG = 'postal-code';

    const POSTAL_CODE_US_TAG = 'zip-code';

    const POSTAL_COTE_MATCH_PATTERN_CANADA = '/\A[A-Z][\d][A-Z][\d][A-Z][\d]\z/';

    const COUNTRY_CODE_MATCH_PATTERN = '/\A([A-Z]\d){3}\z/';

    const MAILING_SCENARIO_TAG = 'mailing-scenario';

    /**
     * Prepare the body of the rates API call.
     *
     * This will render the valid XML request for the Canada Post API request.
     *
     * @param \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return string|bool
     */
    public function getCanadaPostRatesRequestXML(
        \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ): string;

    /**
     * Check Rates Request.
     *
     * Validate that the request has all the needed data to build a valid rates
     * request with the Canada Post Api.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     *
     * @return bool
     */
    public function isRequestValidForCanadaPostRates(
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ): bool;


}
