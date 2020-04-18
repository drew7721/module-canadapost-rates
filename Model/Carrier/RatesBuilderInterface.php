<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Carrier;

/**
 * Interface BuilderInterface
 *
 * Contains the constants needed to build the XML request structure that will
 * be sent to the Canada Post API.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
interface RatesBuilderInterface
{
    public const XMLNS_VALUE = 'http://www.canadapost.ca/ws/ship/rate-v4';

    public const DESTINATION_DOMESTIC = 'domestic';

    public const DESTINATION_USA = 'united-states';

    public const DESTINATION_INTERNATIONAL = 'international';

    public const COUNTRY_CODES = [
        'CA' => self::DESTINATION_DOMESTIC,
        'US' => self::DESTINATION_USA
    ];

    public const POSTAL_CODE_TAG = 'postal-code';

    public const POSTAL_CODE_US_TAG = 'zip-code';

    public const POSTAL_COTE_MATCH_PATTERN_CANADA = '/\A[A-Z][\d][A-Z][\d][A-Z][\d]\z/';

    public const COUNTRY_CODE_MATCH_PATTERN = '/\A([A-Z]\d){3}\z/';

    public const MAILING_SCENARIO_TAG = 'mailing-scenario';
}
