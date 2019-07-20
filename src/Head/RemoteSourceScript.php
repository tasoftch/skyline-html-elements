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

namespace Skyline\HTML\Head;


use Skyline\HTML\Element;
use Skyline\HTML\RenderableTrait;
use Skyline\Render\Template\Extension\TemplateExtensionInterface;

class RemoteSourceScript extends Element implements TemplateExtensionInterface
{
    use RenderableTrait;

    const TYPE_JAVASCRIPT = 'application/javascript';

    public function __construct($content, string $contentType = self::TYPE_JAVASCRIPT, $integrity = NULL, $crossorigin = NULL)
    {
        parent::__construct("script");
        $this["src"] = $content;

        if($integrity)
            $this["integrity"] = $integrity;
        if($crossorigin)
            $this["crossorigin"] = $crossorigin;
        if($contentType)
            $this["type"] = $contentType;
    }

    public function getType(): string
    {
        return $this["type"] ?? "application/octet-stream";
    }

    public function getPosition(): int
    {
        return self::POSITION_HEADER;
    }
}