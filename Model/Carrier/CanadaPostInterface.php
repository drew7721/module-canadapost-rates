<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

interface CanadaPostInterface extends \Magento\Shipping\Model\Carrier\CarrierInterface
{
    const CODE = 'canadapost';

    // ADMIN CONFIGS IDS
    const TITLE = 'title';

    const CUSTOMER_NUMBER = 'customer_number';
}
