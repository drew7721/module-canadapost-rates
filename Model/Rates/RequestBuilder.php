<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Rates;

use JustinKase\CanadaPostRates\Model\Xml\AbstractBuilder;

/**
 * Class RequestBuilder
 *
 * XML document.
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Rates
 */
class RequestBuilder extends AbstractBuilder implements RatesBuilderInterface
{
    /**
     * @param \DOMElement $mainContainer
     */
    protected function buildContent(\DOMElement $mainContainer): void
    {
        // TODO: Implement buildContent() method.
    }
}
