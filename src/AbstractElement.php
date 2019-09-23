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


abstract class AbstractElement extends AbstractBasicElement implements ElementInterface
{
    use RenderableTrait;

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

    /**
     * Creates the indention of a passed level
     * @param int $indention
     * @return string
     */
    protected function getIndentionString(int $indention) {
        return  str_repeat("\t", $indention);
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