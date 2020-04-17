<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model;

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
    const CONTENT_TYPE = 'application/vnd.cpc.ship.rate-v4+xml';

    const ACCEPT = 'application/vnd.cpc.ship.rate-v4+xml';

    const URI_SUFFIX = 'rs/ship/price';

    const METHOD = 'POST';


    /**
     * @inheritDoc
     */
    public function getRequestHeaders(): array
    {
        return [
            'Accept' => $this::ACCEPT,
            'Content-Type' => $this::CONTENT_TYPE,
            'Accept-language' => $this->resolveLocale()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUriSuffix(): string
    {
        return $this::URI_SUFFIX;
    }

    /**
     * @inheritDoc
     */
    public function getRequestMethod(): string
    {
        return $this::METHOD;
    }
}
