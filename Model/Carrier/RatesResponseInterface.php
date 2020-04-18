<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

interface RatesResponseInterface
{
    const X_PATH_QUOTES = 'price-quotes/price-quote';

    const X_PATH_SERVICE_CODE = 'service-code';

    const X_PATH_SERVICE_NAME = 'service-name';

    const X_PATH_PRICE_DETAILS = 'price-details';

    const X_PATH_DUE = 'due';

    const X_PATH_SERVICE_STANDARD = 'service-standard';

    const X_PATH_EXPECTED_DELIVERY_DATE = 'expected-delivery-date';
}
