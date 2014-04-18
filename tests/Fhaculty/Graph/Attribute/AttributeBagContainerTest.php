<?php

use Fhaculty\Graph\Attribute\AttributeBagContainer;

class AttributeBagContainerTest extends TestCase
{
    public function testEmpty()
    {
        $bag = new AttributeBagContainer();

        $this->assertNull($bag->getAttribute('unknown'));
        $this->assertEquals(array(), $bag->getAttributes());
        $this->assertEquals(array(), $bag->getNames());

        $this->assertSame($bag, $bag->getAttributeBag());
    }

    public function testSome()
    {
        $bag = new AttributeBagContainer();

        $bag->setAttribute('true', true);
        $bag->setAttribute('two', 2);

        $this->assertSame(true, $bag->getAttribute('true'));
        $this->assertSame(2, $bag->getAttribute('two'));
        $this->assertEquals(array('true', 'two'), $bag->getNames());

        $bag->setAttribute('float', '1.2');
        $bag->setAttributes(array('two' => 'two', 'three' => 3));

        $expected = array('true' => true, 'two' => 'two', 'float' => 1.2, 'three' => 3);
        $this->assertEquals($expected, $bag->getAttributes());
    }
}
