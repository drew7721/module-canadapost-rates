<?php

/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Api;

use Magento\Tests\NamingConvention\true\string;

/**
 * Class ClientConfig
 *
 * This interface is responsible for supplying the diversified configuration for
 * different api calls.
 *
 * This can be implemented or extended and replaced trough dependency injections
 * as needed to ease the client implementation.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Api
 */
interface ClientConfigInterface
{
    /**
     * Mapping for the language codes.
     *
     * If the language is not detected, it will use the first entry (en-CA).
     *
     * Note that Magento uses `_` not `-`.
     */
    public const HEADER_ACCEPTED_LANGUAGE_MAP = [
        'en-CA' => 'en_GB en_US en_CA en_IN en_AU en_NZ en_ZA',
        'fr-CA' => 'fr_BE fr_CH fr_FR fr_CA fr_LU'
    ];

    // Canada Post API constants
    /**
     * Production or Dev endpoint?
     *
     * @see $self::REQUEST_MODE
     */
    public const API_ENDPOINTS_DOMAINS = [
        0 => 'ct.soa-gw.canadapost.ca',
        1 => 'soa-gw.canadapost.ca'
    ];

    public const URI_PRINT_TEMPLATE = 'https://%s/';

    /**
     * Return the request Uri that the Client will connect to.
     *
     * @return string
     */
    public function getRequestUri(): string;

    /**
     * Get the header authentication token.
     *
     * @return array
     */
    public function getAuthorizationArray(): array;

    /**
     * Get the current locale to send to Canada Post.
     *
     * Only FR and EN. The values are mapped. It will default to english.
     *
     * @return string
     */
    public function resolveLocale(): string;

    /**
     * This returns the headers that are needed for the request.
     *
     * Other than the authentication, headers such as content-type and
     * accept-type might change for each request. This will need to be set for
     * each type of request.
     *
     * @return array
     */
    public function getRequestHeaders(): array;

    /**
     * Return the suffix of the URI for the API call.
     *
     * Implement this for each different endpoint.
     *
     * @return string
     */
    public function getUriSuffix(): string;

    /**
     * Get the request method.
     *
     * Common are POST and GET. They might be different based on the endpoint.
     *
     * @return string
     */
    public function getRequestMethod(): string;

    /**
     * Retrieve the customer number from the configs.
     *
     * In some API calls the customer number is part of the Uri suffix. In those
     * cases, use this method to incorporate it to the Uri suffix.
     *
     * @return string
     */
    public function getCustomerNumber(): string;
}
