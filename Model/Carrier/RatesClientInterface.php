<?php
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
