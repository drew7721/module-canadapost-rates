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

/**
 * Class RequestBuilder
 *
 * TODO: Move code from the Carrier to this class for building the rates request
 * XML document.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Rates
 */
class RequestBuilder implements RatesBuilderInterface
{

    public function getCanadaPostRatesRequestXML(
        \Magento\Shipping\Model\Carrier\AbstractCarrierInterface $carrier,
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ): string {
        // TODO: Implement.
    }

    public function isRequestValidForCanadaPostRates(
        \Magento\Quote\Model\Quote\Address\RateRequest $request
    ): bool {
        // TODO: Implement isRequestValidForCanadaPostRates() method.
    }
}
