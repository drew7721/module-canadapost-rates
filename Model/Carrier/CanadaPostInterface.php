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
    const REQUEST_MODE = 'request_mode';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const CUSTOMER_NUMBER = 'customer_number';

    // Canada Post API constants
    /**
     * Production or Dev endpoint?
     *
     * @see $self::REQUEST_MODE
     */
    const API_ENDPOINTS = [
        0 => 'ct.soa-gw.canadapost.ca',
        1 => 'soa-gw.canadapost.ca'
    ];

    /**
     * Mapping for the language codes.
     *
     * If the language is not detected, it will use the first entry (en-CA).
     *
     * Note that Magento uses `_` not `-`.
     */
    const HEADER_ACCEPTED_LANGUAGE_MAP = [
        'en-CA' => 'en_GB en_US en_CA en_IN en_AU en_NZ en_ZA',
        'fr-CA' => 'fr_BE fr_CH fr_FR fr_CA fr_LU'
    ];
}
