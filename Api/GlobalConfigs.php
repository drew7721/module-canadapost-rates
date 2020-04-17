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
 * Interface GlobalConfigs
 *
 * Mainly to use as a global interface to retrive different configurations from
 * the scope. This can be used during the API call, during the building of the
 * request body or while reading the request response.
 *
 * Note that request body and response constants should NOT be included in this
 * file. This file should only contain static values to ease the retrieval of
 * configuration values.
 *
 * This should contain all the keys in the config.xml file and the ones that are
 * relevant but not there.
 *
 * Configurations in sub-modules or modules that depend on this module can also
 * be added here as long as they are relevant and prefixed with the name of the
 * module that they relate to. The other option is to create another module
 * specific configuration interface with the constants for the module that uses
 * them.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Api
 */
interface GlobalConfigs
{
    // Global defaults
    public const CARRIER_CODE = 'canadapost';

    // Global admin configs
    public const GLOBAL_REQUEST_MODE = 'request_mode';

    public const GLOBAL_CARRIER_TITLE = 'title';

    public const GLOBAL_CUSTOMER_NUMBER = 'customer_number';

    public const GLOBAL_API_USERNAME = 'username';

    public const GLOBAL_API_PASSWORD = 'password';

}
