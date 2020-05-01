<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Model\Xml;

use JustinKase\CanadaPostRates\Api\XmlBuilderInterface;
use JustinKase\CanadaPostRates\Model\Carrier\CanadaPost;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class AbstractBuilder
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Xml
 */
abstract class AbstractBuilder implements XmlBuilderInterface
{
    /** @var \DOMDocument $document */
    protected $document;

    /** @var string $mainElementName */
    protected $mainElementName = null;

    /** @var string $mainElementXmlns */
    protected $mainElementXmlns = null;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface scopeConfig
     */
    private $scopeConfig;

    /**
     * AbstractBuilder constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->document = new \DOMDocument('1.0', 'UTF-8');
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return string
     */
    public function build(RateRequest $rateRequest)
    {
        $mainContainer = $this->createMainContentElement();
        $this->buildContent($mainContainer, $rateRequest);

        return (string) $this;
    }

    /**
     * Responsible for building the XML nodes.
     *
     * Extend this to build all XML request for the Canada Post API.
     *
     * @param \DOMElement $mainContainer
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     */
    abstract protected function buildContent(
        \DOMElement $mainContainer,
        \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
    ): void;

    /**
     * Creates main content Node.
     *
     * This will return the main content node. Include all other elements in
     * this node. It will already be added to the document.
     *
     * @return \DOMElement
     */
    private function createMainContentElement()
    {
        $mainContent = $this->element(
            $this->mainElementName ?? self::DEFAULT_MAIN_CONTENT_ELEMENT_NAME
        );

        if ($this->mainElementXmlns) {
            $mainContent->setAttribute('xmlns', $this->mainElementXmlns);
        }

        $this->document->appendChild($mainContent);

        return $mainContent;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->document->saveXML();
    }

    /**
     * @inheritDoc
     */
    public function element(string $name, string $value = null): \DOMElement
    {
        return $this->document->createElement($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function appendChild(\DOMNode $parent, \DOMElement $child): \DOMNode
    {
        $parent->appendChild($child);

        return $parent;
    }

    /**
     * @inheritDoc
     */
    public function appendChildren(\DOMNode $parent, array $children): \DOMNode
    {
        foreach ($children as $child) {
            $parent->appendChild($child);
        }

        return  $parent;
    }

    /**
     * Convert pounds to Kgs
     *
     * @param float|int $pounds
     *
     * @return float
     */
    public static function convertPoundToKg($pounds)
    {
        return round($pounds / 2.2046, 3);
    }

    /**
     * Get Canada Post Config Data
     *
     * @param $field
     *
     * @return mixed
     */
    public function getConfigData($field)
    {
        $path = 'carriers/' . CanadaPost::CODE . '/' . $field;

        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
