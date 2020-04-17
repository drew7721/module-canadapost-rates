<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Api;

/**
 * Interface Client
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Api
 */
interface Client extends \GuzzleHttp\ClientInterface
{
    /**
     * Call the Canada Post Api
     *
     * This method must be implemented and used to place the call with the
     * given options if any required.
     *
     * @param array $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function requestCanadaPostApi(array $options = []): \Psr\Http\Message\ResponseInterface;
}
