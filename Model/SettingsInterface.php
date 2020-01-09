<?php
namespace JustinKase\CanadaPostRates\Model;

/**
 * Interface SettingsInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model
 */
interface SettingsInterface
{
    /**
     * Production or Dev endpoints.
     */
    const API_ENDPOINTS = [
        0 => 'ct.soa-gw.canadapost.ca',
        1 => 'soa-gw.canadapost.ca'
    ];

    /**
     * This is required for the request headers.
     */
    const HEADER_CONTENT_TYPE = 'application/vnd.cpc.ship.rate-v4+xml';

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

    /**
     * Sprint template for the current module configs.
     */
    const CONFIG_PATH = 'carriers/canadapost/%s';

    // ADMIN CONFIGS IDS
    /**
     * The title of the method. Can be changed in the admin.
     */
    const TITLE = 'title';

    /**
     * Production or Developer?
     *
     * This will influence what API_ENDPOINT will be used in the const above.
     */
    const REQUEST_MODE = 'request_mode';

    /**
     * Canada Post API username.
     */
    const USERNAME = 'username';

    /**
     * Canada Post API password.
     */
    const PASSWORD = 'password';

    /**
     * Customer number from Canada Post.
     */
    const CUSTOMER_NUMBER = 'customer_number';
}
