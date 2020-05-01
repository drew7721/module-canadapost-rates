<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Rates;

use JustinKase\CanadaPostRates\Api\GlobalConfigs;
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
    protected $mainElementName = 'mailing-scenario';

    protected $mainElementXmlns = 'http://www.canadapost.ca/ws/ship/rate-v4';
    /**
     * @var string destinationTag
     */
    protected $destinationTag;
    /**
     * @var string postalCodeTag
     */
    protected $postalCodeTag;
    protected $countryCode;
    protected $postalCode;

    /**
     * @param \DOMElement $mainContainer
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     */
    protected function buildContent(
        \DOMElement $mainContainer,
        \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
    ): void {
        if ($this->isValidRateRequest($rateRequest)) {
            $customerNumber = $this->getConfigData(GlobalConfigs::GLOBAL_CUSTOMER_NUMBER);
            if (!empty($customerNumber)) {
                $mainContainer->appendChild(
                    $this->element(
                        'customer-number',
                        $customerNumber
                    )
                );
            }

            $originalPostalCode = $rateRequest->getOrigPostcode() ?: $rateRequest->getPostcode();
            $mainContainer->appendChild(
                $this->element(
                    'origin-postal-code',
                    $originalPostalCode
                )
            );

            $parcel = $this->element('parcel-characteristics');

            $parcel->appendChild(
                $this->element('weight', $rateRequest->getPackageWeight())
            );
            $mainContainer->appendChild($parcel);

            $destination = $this->element('destination');
            $destinationType = $this->element($this->destinationTag);
            $destination->appendChild($destinationType);

            $destinationPostalCode = $this->element(
                self::POSTAL_CODE_TAG,
                $this->postalCode
            );
            $destinationType->appendChild($destinationPostalCode);

            if ($this->destinationTag === self::DESTINATION_INTERNATIONAL) {
                $destinationCountryCode = $this->element(
                    'country-code',
                    $this->countryCode
                );
                $destinationType->appendChild($destinationCountryCode);
            }

            $mainContainer->appendChild($destination);
        }
    }


    /**
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return bool
     */
    private function isValidRateRequest(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        $destinationCountryCode = $rateRequest->getDestCountryId();
        if (empty($destinationCountryCode)) {
            return false;
        } else {
            $this->setCountryCode($destinationCountryCode);
            if (array_key_exists($this->countryCode, self::COUNTRY_CODES)) {
                $this->destinationTag = self::COUNTRY_CODES[$this->countryCode];
            }

            $valid = preg_match(self::COUNTRY_CODE_MATCH_PATTERN, $this->postalCode);
            if ($valid === false) {
                return false;
            }

            if ($this->destinationTag === self::DESTINATION_USA) {
                $this->postalCodeTag = self::POSTAL_CODE_US_TAG;
            }
        }

        /** @var string $destinationPostalCode */
        $destinationPostalCode = $rateRequest->getDestPostcode();
        if (empty($destinationPostalCode) && $this->destinationTag !== self::DESTINATION_INTERNATIONAL) {
            return false;
        } else {
            $this->setPostalCode($destinationPostalCode);

            if ($this->destinationTag === self::DESTINATION_DOMESTIC) {
                $valid = preg_match(self::POSTAL_COTE_MATCH_PATTERN_CANADA, $this->postalCode);
                if ($valid === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Set the postal code.
     *
     * API requires this to be uppercase and no special chars.
     *
     * @param string $postalCode
     *
     * @return $this
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = preg_replace(
            '/\W/',
            '',
            strtoupper($postalCode)
        );

        return $this;
    }


    /**
     * Set the country code.
     *
     * This needs to be a valid 2 uppercase letters country code.
     *
     * The request is build differently based on this.
     *
     * US => united-states
     * CA => domestic
     * OTHER => international
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = preg_replace(
            '/(\W|\d)/',
            '',
            strtoupper($countryCode)
        );

        return $this;
    }
}
