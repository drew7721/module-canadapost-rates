<?php
/**
 * Copyright © 2020
 * @copyright Alex Ghiban & JustinKase.ca - All rights reserved.
 * @license GPL-3.0-only
 * @see https://justinkase.ca or https://ghiban.com
 * @contact <alex@justinkase.ca>
 */

namespace JustinKase\CanadaPostRates\Api;

/**
 * Interface XmlBuilderInterface
 *
 * @author Alex Ghiban <drew7721@gmail.com>
 *
 * @package JustinKase\CanadaPostRates\Model\Xml
 */
interface XmlBuilderInterface
{
    /**
     * This will be used for the main container name if none is set.
     *
     */
    const DEFAULT_MAIN_CONTENT_ELEMENT_NAME = 'body';

    /**
     * Main
     * @return string
     */
    public function build();

    /**
     * Returns a new Element.
     *
     * @param string $name
     * @param string $value
     *
     * @return \DOMElement
     */
    public function element(string $name, string $value = null): \DOMElement;

    /**
     * @param \DOMNode $parent
     * @param \DOMElement $child
     *
     * @return \DOMNode
     */
    public function appendChild(\DOMNode $parent, \DOMElement $child): \DOMNode;

    /**
     * Append multiple children.
     *
     * This will return the parent node.
     *
     * @param \DOMNode $parent
     * @param \DOMElement[] $children
     *
     * @return \DOMNode
     */
    public function appendChildren(\DOMNode $parent, array $children): \DOMNode;
}
