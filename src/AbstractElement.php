<?php
/**
 * Copyright (c) 2019 TASoft Applications, Th. Abplanalp <info@tasoft.ch>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Skyline\HTML;


use Skyline\HTML\Exception\DisallowedContentsException;
use Skyline\HTML\Exception\ElementException;

abstract class AbstractElement implements ElementInterface
{
    /**
     * Define, if the element allows other elements as contents.
     * @var bool
     */
    protected $allowsContent = true;

    /** @var string HTML tag name */
    protected $tagName = 'div';
    /** @var string[] HTML element attributes */
    protected $attributes = [];

    /** @var AbstractElement[] */
    protected $childElements = [];

    /** @var AbstractElement|null */
    protected $parentElement;

    protected $autoIdentificationEnabled = true;

    /**
     * AbstractElement constructor.
     * @param bool $allowsContent
     * @param string $tagName
     */
    public function __construct(string $tagName = 'div', bool $allowsContent = true)
    {
        $this->allowsContent = $allowsContent;
        $this->tagName = $tagName;
    }

    /**
     * Sets an id
     *
     * @param string $id
     */
    public function setID(string $id) {
        $this["id"] = $id;
    }

    /**
     * Get the element id
     *
     * @return string
     */
    public function getID(): string {
        return $this["id"] ?? "";
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[strtolower($offset)]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->attributes[strtolower($offset)] ?? NULL;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if(is_scalar($value))
            $this->attributes[strtolower($offset)] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if(isset($this->attributes[$offset = strtolower($offset)]))
            unset($this->attributes[$offset]);
    }

    /**
     * @return AbstractElement[]
     */
    public function getChildElements(): array
    {
        return $this->childElements;
    }

    /**
     * Searches for an element with $anID recursively.
     *
     * @param string $anID
     * @return AbstractElement|null
     */
    public function getElementByID(string $anID): ?AbstractElement {
        $iterator = function(AbstractElement $element) use (&$iterator, $anID) {
            if($element["id"] == $anID)
                yield $element;
            elseif($children = $element->getChildElements()) {
                foreach($children as $child)
                    yield from $iterator($child);
            }
        };

        foreach ($iterator as $element)
            return $element;
        return NULL;
    }

    /**
     * Appends a child element
     *
     * @param AbstractElement $childElement
     * @throws DisallowedContentsException
     * @throws ElementException
     */
    public function appendChild(AbstractElement $childElement) {
        if(!in_array($childElement, $this->childElements)) {
            if(!$this->isContentAllowed()) {
                $e = new DisallowedContentsException("Element does not allow contents");
                $e->setElement($this);
                throw $e;
            }
            if($childElement->parentElement) {
                $e = new ElementException("Element is already child of other element");
                $e->setElement($childElement);
                throw $e;
            }
            $childElement->parentElement = $this;
            $this->childElements[] = $childElement;
            if(!$childElement->getID() && $this->isAutoIdentificationEnabled())
                $childElement->setID( 'e_' . uniqid() );
        }
    }

    /**
     * Removes an element from list
     *
     * @param AbstractElement $element
     */
    public function removeChild(AbstractElement $element) {
        if($element->parentElement === $this) {
            if(($idx = array_search($element, $this->childElements)) !== false) {
                $element->parentElement = NULL;
                unset($this->childElements[$idx]);
            }
        }
    }

    /**
     * @return AbstractElement|null
     */
    public function getParentElement(): ?AbstractElement
    {
        return $this->parentElement;
    }

    /**
     * @return bool
     */
    public function isContentAllowed(): bool
    {
        return $this->allowsContent;
    }

    /**
     * @return string
     */
    public function getTagName(): string
    {
        return $this->tagName;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Transforms a HTML element into its HTML string representation
     *
     * @param int $indention
     * @return string
     */
    public function toString(int $indention = 0): string {
        $str = $this->stringifyStart($indention);
        $str.=$this->stringifyContents($indention+1);
        $str.=$this->stringifyEnd($indention);

        return $str;
    }

    /**
     * @return bool
     */
    public function isAutoIdentificationEnabled(): bool
    {
        return $this->autoIdentificationEnabled;
    }

    /**
     * @param bool $autoIdentificationEnabled
     */
    public function setAutoIdentificationEnabled(bool $autoIdentificationEnabled): void
    {
        $this->autoIdentificationEnabled = $autoIdentificationEnabled;
        array_walk($this->childElements, function(AbstractElement $element) {
            $element->setAutoIdentificationEnabled( $this->isAutoIdentificationEnabled() );
        });
    }

    /**
     * Deep copy child elements
     */
    public function __clone()
    {
        $this->setID("");
        foreach($this->childElements as &$element) {
            $element = clone $element;
            $element->parentElement = $this;

            if($this->isAutoIdentificationEnabled())
                $element->setID("e_".uniqid());
        }
    }

    /**
     * Creates the indention of a passed level
     * @param int $indention
     * @return string
     */
    protected function getIndentionString(int $indention) {
        return  str_repeat("\t", $indention);
    }

    /**
     * @param int $indention
     * @return string
     */
    abstract protected function stringifyStart(int $indention = 0): string;

    /**
     * @param int $indention
     * @return string
     */
    abstract protected function stringifyEnd(int $indention = 0): string;

    /**
     * Called by toString method to transform elements content into a HTML string
     *
     * @param int $indention
     * @return string
     */
    protected function stringifyContents(int $indention = 0): string {
        if($this->isContentAllowed() && ($children = $this->getChildElements())) {
            $str = "";
            foreach($children as $element) {
                $str .= $element->toString($indention+1) . PHP_EOL;
            }
            return rtrim($str, PHP_EOL);
        }
        return "";
    }

    /**
     * Every plain text value for attribute contents is passed to this method for escaping reasons
     *
     * @param $value
     * @return string|null
     */
    protected function escapedAttributeValue($value): ?string {
        return htmlspecialchars($value);
    }

    /**
     * Every plain text value for text contents is passed to this method for escaping reasons
     *
     * @param $value
     * @return string|null
     */
    protected function escapedContentValue($value): ?string {
        return htmlspecialchars($value);
    }
}