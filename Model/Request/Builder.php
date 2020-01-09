<?php
namespace JustinKase\CanadaPostRates\Model\Request;

/**
 * Class Builder
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 */
class Builder implements BuilderInterface
{
    /**
     * @var \JustinKase\CanadaPostRates\Model\Settings
     */
    private $settings;

    // Mandatory Values
    /**
     * Weight in Kg. Default 10Kg.
     * @var float $weight
     */
    private $weight = 10.0;

    private $postalCode = null;

    private $countryCode = null;

    private $destinationTag = self::DESTINATION_INTERNATIONAL;

    private $postalCodeTag = self::POSTAL_CODE_TAG;

    /**
     * Builder constructor.
     *
     * @param \JustinKase\CanadaPostRates\Model\Settings $settings
     */
    public function __construct(
        \JustinKase\CanadaPostRates\Model\Settings $settings
    ) {
        $this->settings = $settings;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClientForCanadaPostRateRequest()
    {
        return (new \GuzzleHttp\Client([
            'base_uri' => $this->settings->getEndpoint(),
            'auth' => $this->settings->getAuth(),
            'headers' => $this->settings->getRequestHeaders(),
        ]));
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return bool|string
     * @throws \Exception
     */
    public function getXMLBodyForCanadaPostRateRequest(
        \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
    ) {
        $result = false;

        if ($this->isValidRateRequest($rateRequest)) {
            $result = $this->buildXmlRequest($rateRequest);
        }

        return $result;
    }

    /**
     * @param null $weight
     *
     * @return Builder
     */
    public function setWeight($weight)
    {
        if (!empty($weight)) {
            $this->weight = sprintf('%3.1f', $weight);
        }

        return $this;
    }

    /**
     * Set the postal code.
     *
     * API requires this to be uppercase and no special chars.
     *
     * @param string $postalCode
     *
     * @return Builder
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
     * @return Builder
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

        $this->setWeight($rateRequest->getPackageWeight());

        return true;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return string
     * @throws \Exception
     */
    private function buildXmlRequest(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        $xmlDocument = new \DOMDocument('1.0', 'UTF-8');
        $scenario = $xmlDocument->createElement(self::MAILING_SCENARIO_TAG);

        $scenario->setAttribute(
            'xmlns',
            self::XMLNS_VALUE
        );

        $xmlDocument->appendChild($scenario);

        $customerNumber = $this->settings->getCustomerNumber();
        if (!empty($customerNumber)) {
            $scenario->appendChild(
                $xmlDocument->createElement('customer-number', $customerNumber)
            );
        }

        $originalPostalCode = $rateRequest->getOrigPostcode() ?: $rateRequest->getPostcode();

        $scenario->appendChild(
            $xmlDocument->createElement(
                'origin-postal-code',
                $originalPostalCode
            )
        );

        $parcel = $xmlDocument->createElement('parcel-characteristics');

        $parcel->appendChild(
            $xmlDocument->createElement('weight', $this->weight)
        );
        $scenario->appendChild($parcel);

        $destination = $xmlDocument->createElement('destination');
        $destinationType = $xmlDocument->createElement($this->destinationTag);
        $destination->appendChild($destinationType);

        $destinationPostalCode = $xmlDocument->createElement(
            $this->postalCodeTag,
            $this->postalCode
        );
        $destinationType->appendChild($destinationPostalCode);

        if ($this->destinationTag === self::DESTINATION_INTERNATIONAL) {
            $destinationCountryCode = $xmlDocument->createElement(
                'country-code',
                $this->countryCode
            );
            $destinationType->appendChild($destinationCountryCode);
        }

        $scenario->appendChild($destination);

        return $xmlDocument->saveXML();
    }
}
