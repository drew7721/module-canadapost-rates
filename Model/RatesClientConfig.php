<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model;

use JustinKase\CanadaPostRates\Model\Carrier\RatesClientInterface;

/**
 * Class RatesClientConfig
 *
 * Client for Canada Post Rates.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model
 */
class RatesClientConfig extends AbstractClientConfig
{
    public const CONTENT_TYPE = 'application/vnd.cpc.ship.rate-v4+xml';

    public const ACCEPT = 'application/vnd.cpc.ship.rate-v4+xml';

    public const URI_SUFFIX = 'rs/ship/price';

    public const METHOD = 'POST';

    /**
     * @inheritDoc
     */
    public function getRequestHeaders(): array
    {
        return [
            'Accept' => self::ACCEPT,
            'Content-Type' => self::CONTENT_TYPE,
            'Accept-language' => $this->resolveLocale()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUriSuffix(): string
    {
        return self::URI_SUFFIX;
    }

    /**
     * @inheritDoc
     */
    public function getRequestMethod(): string
    {
        return self::METHOD;
    }
}
