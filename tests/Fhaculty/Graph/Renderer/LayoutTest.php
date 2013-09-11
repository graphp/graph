<?php

use Fhaculty\Graph\Renderer\Layout;

class LayoutTest extends TestCase
{
    public function testEmptyLayout()
    {
        $layout = new Layout();

        $this->assertEquals(array(), $layout->getAttributes());
        $this->assertFalse($layout->hasAttribute('non-existant'));

        return $layout;
    }

    /**
     * @depends testEmptyLayout
     * @expectedException OutOfBoundsException
     * @param Layout $layout
     */
    public function testEmptyFailsInvalid(Layout $layout)
    {
        $layout->getAttribute('non-existant');
    }

    public function testOverwriting()
    {
        $layout = new Layout();

        $layout->setAttribute('a', 'a');
        $this->assertEquals(array('a' => 'a'), $layout->getAttributes());

        $layout->setAttribute('b', 'b');
        $this->assertEquals(array('a' => 'a', 'b' => 'b'), $layout->getAttributes());

        $layout->setAttribute('a', 'b');
        $layout->setAttribute('b', null);
        $this->assertEquals(array('a' => 'b'), $layout->getAttributes());
        $this->assertEquals('b', $layout->getAttribute('a'));

        $layout->setAttributes(array('c' => 'c', 'a' => null));
        $this->assertEquals(array('c' => 'c'), $layout->getAttributes());
    }
}
