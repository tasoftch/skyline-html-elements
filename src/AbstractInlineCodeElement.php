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


use Skyline\Render\Template\TemplateInterface;

/**
 * Inline render templates can be created directly on demand. All build-in inline coded templates already have a default html output.
 * Use them like: $template = new InlineTemplate(function($info) {?> ... html ... <?});
 * while $info is anything passed by the render context's renderSubTemplate($tmp, $info);
 * @package Skyline\HTML
 */
abstract class AbstractInlineCodeElement extends AbstractElement implements TemplateInterface
{
    /** @var callable */
    private $callback;
    /** @var string */
    private $name = "";

    /**
     * Creates a new inline render template
     * @param string|null $name
     * @param callable|NULL $callback
     */
    public function __construct(callable $callback = NULL, string $name = "")
    {
        parent::__construct($name, false);
        $this->callback = $callback;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * If the template did not yet specify a inline html callback, use the default one.
     *
     * @return callable
     */
    abstract protected function makeDefaultCallback(): callable;

    /**
     * @inheritDoc
     */
    public function getRenderable(): callable
    {
        if(!$this->callback)
            $this->callback = $this->makeDefaultCallback();
        return $this->callback;
    }
}