<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model;

/**
 * Class Client
 *
 * This is the base client to call the Canada Post API.
 *
 * It uses Guzzle Http to connect. This client will be used for all the calls
 * to the API.
 *
 * As multiple endpoints need to be called during different transactions this
 * should not implement the endpoint it will use. Instead, the endpoint will be
 * provided by the call.
 *
 * However, all calls need to be authenticated. This will maintain a global
 * authentication as well as consider the proper endpoint based on the current
 * setting between production and development.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model
 */
class Client
{

}
