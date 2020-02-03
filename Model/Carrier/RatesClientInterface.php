<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

/**
 * Class RatesInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
interface RatesClientInterface
{
    const RATE_REQUEST_METHOD = 'POST';

    /**
     * This is required for the request headers.
     */
    const RATES_HEADER_CONTENT_TYPE = 'application/vnd.cpc.ship.rate-v4+xml';
}
