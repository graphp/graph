<?php

namespace Fhaculty\Graph\Tests;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Group;
use Fhaculty\Graph\Tests\Attribute\AbstractAttributeAwareTest;

final class GroupTest extends AbstractAttributeAwareTest
{
    public function testGroupIdCanBeAnInteger()
    {
        $group = new Group(new Graph(), 1);

        $this->assertSame(1, $group->getId());
    }

    public function testGroupIdCanBeAString()
    {
        $group = new Group(new Graph(), 'string');

        $this->assertSame('string', $group->getId());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGroupIdCanNotBeSomethingElse()
    {
        new Group(new Graph(), null);
    }

    public function testGroupEquals()
    {
        $group = new Group(new Graph(), 1);

        $this->assertTrue($group->equals(new Group(new Graph(), 1)));
    }

    public function testGroupIsNotEqual()
    {
        $group = new Group(new Graph(), 1);

        $this->assertFalse($group->equals(new Group(new Graph(), 2)));
    }

    public function testFindAllVerticesInGroup()
    {
        $graph = new Graph();
        $group1 = $graph->createGroup(1);
        $vertex1 = $graph->createVertex(1)->setGroup($group1);
        $vertex2 = $graph->createVertex(2)->setGroup($group1);
        $group2 = $graph->createGroup(2);
        $vertex3 = $graph->createVertex(3)->setGroup($group2);

        $this->assertEquals(
            array(
                $vertex1,
                $vertex2
            ),
            $group1->getVerticesInGroup()
        );

        $this->assertEquals(
            array(
                $vertex3
            ),
            $group2->getVerticesInGroup()
        );
    }

    protected function createAttributeAware()
    {
        return new Group(new Graph(), 1);
    }
}
