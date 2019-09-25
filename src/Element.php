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


class Element extends AbstractElement
{
    /** @var bool Enabled automatical identification of child elements */
    protected $autoIdentificationEnabled = false;
    protected $formatOutput = true;
    protected $skipInlineFormat = false;


    public function getID(): string
    {
        if(!($id = parent::getID()) && $this->isAutoIdentificationEnabled()) {
            $this->setID($id = 'e_'.uniqid());
        }
        return $id;
    }

    /**
     * Defines passed classes as HTML class attribute
     *
     * @param array $classes
     */
    public function setClasses(array $classes) {
        $this["class"] = implode(" ", $classes);
    }

    /**
     * Get HTML class attribute as classes
     *
     * @return array
     */
    public function getClasses(): array {
        return explode(" ", preg_replace('/\s+/i', " ", $this->getClassName()));
    }

    /**
     * Get HTML class attribute as plain string
     *
     * @return string
     */
    public function getClassName(): string {
        return $this["class"] ?? "";
    }

    /**
     * Adds a class to HTML class attribute if not exist yet
     *
     * @param string $class
     */
    public function addClass(string $class) {
        $classes = $this->getClasses();
        if(!in_array($class, $classes))
            $classes[] = $class;
        $this->setClasses($classes);
    }

    /**
     * Removes a class from HTML class attribute list
     *
     * @param string $class
     */
    public function removeClass(string $class) {
        $classes = $this->getClasses();
        if(($idx = array_search($class, $classes)) !== false)
            unset($classes[$idx]);
        $this->setClasses($classes);
    }

    /**
     * Checks if a class exists in list
     *
     * @param string $class
     * @return bool
     */
    public function containsClass(string $class): bool {
        return in_array($class, $this->getClasses());
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

        foreach ($iterator($this) as $element)
            return $element;
        return NULL;
    }

    /**
     * As used in JS this method searches all elements matching a CSS class
     *
     * @param string $class
     * @return array
     */
    public function getElementsByClass(string $class): array {
        $iterator = function(Element $element) use (&$iterator, $class) {
            if($element instanceof Element && $element->containsClass($class))
                yield $element;

            if($children = $element->getChildElements()) {
                foreach($children as $child)
                    yield from $iterator($child);
            }
        };

        $elements = [];
        foreach ($iterator($this) as $element)
            $elements[] = $element;
        return $elements;
    }

    /**
     * @return bool
     */
    public function formatOutput(): bool
    {
        return $this->formatOutput;
    }

    /**
     * @param bool $formatOutput
     */
    public function setFormatOutput(bool $formatOutput): void
    {
        $this->formatOutput = $formatOutput;
        array_walk($this->childElements, function(Element $element) {
            if($element instanceof Element)
                $element->setFormatOutput( $this->formatOutput() );
        });
    }

    /**
     * If returns true, the element does not apply format for child elements.
     * Each tag is on a new line and its content indended.
     * If true, it does not add new lines and also does not indend further elements.
     *
     * @return bool
     */
    protected function skipInlineFormat(): bool {
        return $this->skipInlineFormat;
    }

    /**
     * @param bool $skipInlineFormat
     */
    public function setSkipInlineFormat(bool $skipInlineFormat): void
    {
        $this->skipInlineFormat = $skipInlineFormat;
    }

    protected function stringifyStart(int $indention = 0): string
    {
        $ind = $this->formatOutput() ? $this->getIndentionString($indention) : "";
        $args = "";
        $nl = $this->formatOutput() && !$this->skipInlineFormat() ? PHP_EOL : "";

        if(count($attributes = $this->getAttributes())) {
            $arguments = [];

            foreach($attributes as $key => $value) {
                if($value === true || $value === false) {
                    if($value)
                        $arguments[] = $this->escapedAttributeValue( $key );
                    continue;
                }
                else {
                    $key = $this->escapedAttributeValue($key);
                    if(!$key)
                        continue;
                    $value = $this->escapedAttributeValue($value);
                    if($value === NULL)
                        continue;
                    $arguments[] = sprintf("%s=\"%s\"", $key, $value);
                }
            }
            $args = " " . implode(" ", $arguments);
        }

        if($this->isContentAllowed())
            return sprintf("%s<%s%s>$nl", $ind, $this->getTagName(), $args);
        else
            return sprintf("%s<%s%s/>$nl", $ind, $this->getTagName(), $args);
    }

    protected function stringifyEnd(int $indention = 0): string
    {
        if($this->isContentAllowed()) {
            $ind = $this->formatOutput() ? $this->getIndentionString($indention) : "";
            $nl = $this->formatOutput() ? PHP_EOL : "";

            return sprintf("%s</%s>$nl", $ind, $this->getTagName());
        }
        return "";
    }

    /**
     * @inheritDoc
     */
    public function appendElement(ElementInterface $childElement)
    {
        parent::appendElement($childElement);
        if($childElement instanceof Element) {
            $childElement->setAutoIdentificationEnabled( $this->isAutoIdentificationEnabled() );
            $childElement->setFormatOutput( $this->formatOutput() );
        }
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
        array_walk($this->childElements, function(Element $element) {
            if($element instanceof Element)
                $element->setAutoIdentificationEnabled( $this->isAutoIdentificationEnabled() );
        });
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
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Deep copy child elements
     */
    public function __clone()
    {
        unset($this["id"]);

        foreach($this->getChildElements() as &$element) {
            $element = clone $element;
            $element->setParentElement( $this );
            unset($element["id"]);
        }
    }

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
                $str .= $element->toString($indention);
            }
            return $str;
        }
        return "";
    }
}