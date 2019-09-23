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

/**
 * GeneralElementTest.php
 * skyline-html-elements
 *
 * Created on 2019-06-03 18:58 by thomas
 */

use PHPUnit\Framework\TestCase;
use Skyline\HTML\Element;

class GeneralElementTest extends TestCase
{
    public function testCoreElement() {
        $element = new Element();
        $this->assertEquals('div', $element->getTagName());
        $this->assertTrue($element->isContentAllowed());
    }

    public function testID() {
        $element = new Element();
        $element->setID("myID");
        $this->assertEquals("myID", $element->getID());

        $this->assertEquals([
            "id" => 'myID'
        ], $element->getAttributes());

        $this->assertEquals("myID", $element["ID"]);
        $this->assertEquals("myID", $element["Id"]);

        unset($element["iD"]);

        $this->assertSame("", $element->getID());
    }

    public function testClasses() {
        $element = new Element("p");
        $this->assertEquals("p", $element->getTagName());

        $element->setClasses(["class1", "class2", "class3"]);
        $this->assertEquals("class1 class2 class3", $element->getClassName());
        $element->addClass("class4");
        $element->removeClass("class2");
        $element->removeClass("not-existing");

        $element->addClass("class1");

        $this->assertEquals([
            "class1",
            "class3",
            "class4"
        ], $element->getClasses());

        $this->assertEquals("class1 class3 class4", $element["CLASS"]);
    }

    public function testChildren() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->appendElement($child2);
        $child2->appendElement($child1);

        $this->assertSame($parent, $child2->getParentElement());
        $this->assertSame($child2, $child1->getParentElement());

        $this->assertSame([$child2], $parent->getChildElements());
    }

    /**
     * @expectedException Skyline\HTML\Exception\DisallowedContentsException
     */
    public function testInvalidChildren1() {
        $parent = new Element("div", false);
        $child1 = new Element();
        $parent->appendElement($child1);
    }

    /**
     * @expectedException Skyline\HTML\Exception\ElementException
     */
    public function testInvalidChildren2() {
        $parent = new Element();
        $parent2 = new Element();
        $child = new Element();

        $parent->appendElement($child);
        $parent2->appendElement($child);
    }

    public function testRemoveChild() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->appendElement($child1);
        $child1->appendElement($child2);

        $parent->appendElement($child2);
        $this->assertSame($child1, $child2->getParentElement());

        $parent->appendElement($child1);
        $this->assertSame($child1, $child2->getParentElement());

        $this->assertNull($child1->getParentElement());
    }

    public function testGetElementById() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->setID("p");
        $child1->setID("c1");
        $child2->setID("c2");

        $parent->appendElement($child1);
        $child1->appendElement($child2);

        $this->assertSame($parent, $parent->getElementByID("p"));
        $this->assertSame($child1, $parent->getElementByID("c1"));
        $this->assertSame($child2, $parent->getElementByID("c2"));
    }

    public function testGetElementsByClass() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->addClass("p");
        $child1->addClass("c1");
        $child2->addClass("c2");
        $child2->addClass("p");

        $parent->appendElement($child1);
        $child1->appendElement($child2);

        $this->assertSame([
            $parent, $child2
        ], $parent->getElementsByClass("p"));
    }

    public function testAutoIdentification() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->appendElement($child1);
        $child1->appendElement($child2);

        $this->assertEquals("", $parent->getID());
        $this->assertEquals("", $child1->getID());
        $this->assertEquals("", $child2->getID());

        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->setAutoIdentificationEnabled(true);

        $parent->appendElement($child1);
        $child1->appendElement($child2);

        $this->assertNotEquals("", $parent->getID());
        $this->assertNotEquals("", $child1->getID());
        $this->assertNotEquals("", $child2->getID());

        $parent->setAutoIdentificationEnabled(false);
    }

    public function testClone() {
        $parent = new Element();
        $child1 = new Element();
        $child2 = new Element();

        $parent->appendElement($child1);
        $parent->appendElement($child2);

        $cloned = clone $parent;
        $this->assertNotSame($parent, $cloned);
        $this->assertNotSame($parent->getChildElements(), $cloned->getChildElements());

        $this->assertEquals($parent, $cloned);
        $this->assertEquals($parent->getChildElements(), $cloned->getChildElements());
    }

    public function testToString() {
        $element = new Element("p", false);
        $this->assertEquals("<!--suppress ALL -->
<p/>\n", $element->toString());

        $element = new Element("span", false);
        $element["style"] = 'a: test';
        $element["type"] = true;
        $element["test"] = 2;

        $this->assertEquals("<!--suppress ALL -->
<span style=\"a: test\" type test=\"2\"/>\n", (string)$element);

        $element = new Element("rew", false);
        $element["test1"] = true;
        $element["test2"] = false;
        $element["test"] = 0;

        $this->assertEquals("<!--suppress ALL -->
<rew test1 test=\"0\"/>\n", (string)$element);

        $element = new Element("p");
        $element->setFormatOutput(false);

        $this->assertEquals("<p></p>", $element->toString());
        $element["test1"] = true;
        $element["test2"] = false;
        $element["test"] = 0;
        $this->assertEquals("<p test1 test=\"0\"></p>", $element->toString());

        $child = new Element("t", false);
        $child["label"] = 'test';
        $element->appendElement($child);

        $this->assertEquals("<!--suppress ALL -->
<p test1 test=\"0\"><t label=\"test\"/></p>", $element->toString());
    }
}
