<?php
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

    const RATE_REQUEST_METHOD = 'POST';

    /**
     * This is required for the request headers.
     */
    const RATES_HEADER_CONTENT_TYPE = 'application/vnd.cpc.ship.rate-v4+xml';

    /**
     * Mapping for the language codes.
     *
     * If the language is not detected, it will use the first entry (en-CA).
     *
     * Note that Magento uses `_` not `-`.
     */
    const RATES_HEADER_ACCEPTED_LANGUAGE_MAP = [
        'en-CA' => 'en_GB en_US en_CA en_IN en_AU en_NZ en_ZA',
        'fr-CA' => 'fr_BE fr_CH fr_FR fr_CA fr_LU'
    ];
}
