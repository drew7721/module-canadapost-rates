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
    public const X_PATH_QUOTES = 'price-quotes/price-quote';

    public const X_PATH_SERVICE_CODE = 'service-code';

    public const X_PATH_SERVICE_NAME = 'service-name';

    public const X_PATH_PRICE_DETAILS = 'price-details';

    public const X_PATH_DUE = 'due';

    public const X_PATH_SERVICE_STANDARD = 'service-standard';

    public const X_PATH_EXPECTED_DELIVERY_DATE = 'expected-delivery-date';
}
