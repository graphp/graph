<?php

use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Exception\InvalidArgumentException;

class VerticesTest extends BaseVerticesTest
{
    protected function createVertices(array $vertices)
    {
        return new Vertices($vertices);
    }

    public function testFactoryEmptyArray()
    {
        $vertices = Vertices::factory(array());

        $this->assertInstanceOf('Fhaculty\Graph\Set\Vertices', $vertices);
        $this->assertTrue($vertices->isEmpty());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetVertexOrderInvalidSortBy()
    {
        $graph = new Graph();
        $graph->createVertex(1);

        $vertices = $graph->getVertices();

        $vertices->getVertexOrder('not a valid callback');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetVicesOrderInvalidSortBy()
    {
        $vertices = $this->createVertices(array());

        $vertices->getVerticesOrder('not a valid callback');
    }
}
