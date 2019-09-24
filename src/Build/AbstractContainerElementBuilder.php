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

namespace Skyline\HTML\Build;


use Skyline\HTML\Element;
use Skyline\HTML\ElementInterface;
use Skyline\Render\Context\RenderContextInterface;

abstract class AbstractContainerElementBuilder extends AbstractSingleElementBuilder
{
    /** @var ElementInterface */
    private $finalElement;

    /**
     * @return ElementInterface
     */
    public function getFinalElement(): ElementInterface
    {
        return $this->finalElement;
    }

    /**
     * @inheritDoc
     */
    protected function buildFinalElement(ElementInterface $element, RenderContextInterface $context, $info)
    {
        $this->finalElement = $element;

        $container = $this->buildInitialContainer($context, $info);
        if($noEl = $container ? false : true) {
            $container = new Element("_", true);
        }

        $this->buildFinalContainer($container, $context, $info);

        if($noEl) {
            $str = "";
            foreach($container->getChildElements() as $child)
                $str .= $child->toString();
            return $str;
        } else {
            return $container->toString();
        }
    }

    /**
     * Create a container element to represent the root element.
     * Returning null will create a container as well but not display it.
     *
     * @param RenderContextInterface $context
     * @param $info
     * @return ElementInterface|null
     */
    protected function buildInitialContainer(RenderContextInterface $context, $info): ?ElementInterface {
        return new Element("div", true);
    }

    /**
     * Creates the final container
     *
     * @param ElementInterface $container
     * @param RenderContextInterface $context
     * @param $info
     * @return void
     */
    abstract protected function buildFinalContainer(ElementInterface $container, RenderContextInterface $context, $info);
}