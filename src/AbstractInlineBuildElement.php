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


use Skyline\Render\Context\RenderContextInterface;
use Skyline\Render\Template\ContextControlInterface;
use Skyline\Render\Template\TemplateInterface;

abstract class AbstractInlineBuildElement extends AbstractBasicElement implements TemplateInterface, ContextControlInterface
{
    private $renderContext;

    public function __construct()
    {
        parent::__construct('', false);
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function getID()
    {
        return $this["id"];
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this["name"];
    }

    /**
     * @inheritDoc
     */
    public function getRenderable(): callable
    {
        return function($info) {
            return $this->buildElement($this->renderContext, $info);
        };
    }

    /**
     * Do not bind to context cause it should call itself
     *
     * @inheritDoc
     */
    public function shouldBindToContext(RenderContextInterface $ctx): bool
    {
        $this->renderContext = $ctx;
        return false;
    }

    /**
     * This method is called on render time to build a html element on demand.
     * Normally this kind of elements are used to quickly render snipplets.
     * The $info argument contains the value passed by the render context's renderSubTemplate($template, $info); info argument.
     *
     * @param RenderContextInterface $context
     * @param $info
     * @return string|null
     */
    abstract protected function buildElement(RenderContextInterface $context, $info);
}