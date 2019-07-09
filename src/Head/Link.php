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


use Skyline\HTML\EmptyElement;
use Skyline\Render\Template\Extension\TemplateExtensionInterface;

class Link extends EmptyElement implements TemplateExtensionInterface
{
    const REL_AUTHOR = "author";
    const REL_DNS_PREFETCH = "dns-prefetch";
    const REL_HELP = "help";
    const REL_ICON = 'icon';
    const REL_SHORTCUT = 'shortcut';
    const REL_LICENSE = 'license';
    const REL_NEXT = 'next';
    const REL_PINGBACK = 'pingback';
    const REL_PRECONNECT = 'preconnect';
    const REL_PREFETCH = "prefetch";
    const REL_PRELOAD = 'preload';
    const REL_PRERENDER = 'prerender';
    const REL_PREV = 'prev';
    const REL_SEARCH = 'search';

    public function __construct($content, string $relation, string $contentType = NULL)
    {
        parent::__construct("link");
        $this["rel"] = $relation;
        $this["href"] = $content;
        if($contentType)
            $this["type"] = $contentType;
    }

    public function getType(): string
    {
        return $this["type"] ?? "text/html";
    }

    public function getPosition(): int
    {
        return self::POSITION_HEADER;
    }
}