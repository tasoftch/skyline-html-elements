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
    use RenderableTrait;

    /**
     * Define, if the element allows other elements as contents.
     * @var bool
     */
    protected $allowsContent = true;

    /** @var string HTML tag name */
    protected $tagName = 'div';
    /** @var string[] HTML element attributes */
    protected $attributes = [];

    /** @var ElementInterface[] */
    protected $childElements = [];

    /** @var ElementInterface|null */
    protected $parentElement;

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
     * @param array $children
     */
    public function setChildElements(array $children)
    {
        $this->childElements = $children;
    }

    /**
     * Appends a child element
     *
     * @param ElementInterface $childElement
     * @throws DisallowedContentsException
     * @throws ElementException
     */
    public function appendChild(ElementInterface $childElement) {
        if(!in_array($childElement, $this->getChildElements())) {
            if(!$this->isContentAllowed()) {
                $e = new DisallowedContentsException("Element does not allow contents");
                $e->setElement($this);
                throw $e;
            }
            if($childElement->getParentElement()) {
                $e = new ElementException("Element is already child of other element");
                $e->setElement($childElement);
                throw $e;
            }
            $childElement->parentElement = $this;
            $this->childElements[] = $childElement;
        }
    }

    /**
     * Removes an element from list
     *
     * @param AbstractElement $element
     */
    public function removeChild(ElementInterface $element) {
        if($element->getParentElement() === $this) {
            if(($idx = array_search($element, $children = $this->getChildElements())) !== false) {
                $element->setParentElement(NULL);
                unset($children[$idx]);
                $this->setChildElements($children);
            }
        }
    }

    /**
     * @return ElementInterface|null
     */
    public function getParentElement(): ?ElementInterface
    {
        return $this->parentElement;
    }

    /**
     * @param ElementInterface|null $element
     */
    public function setParentElement(?ElementInterface $element)
    {
        $this->parentElement = $element;
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
     * Sets an HTML id attribute
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
    public function toString(): string
    {
        return "";
    }
}