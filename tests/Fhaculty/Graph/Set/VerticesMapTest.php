<?php

use Fhaculty\Graph\Set\VerticesMap;
use Fhaculty\Graph\Graph;

class VerticesMapTest extends BaseVerticesTest
{
    protected function createVertices(array $vertices)
    {
        return new VerticesMap($vertices);
    }

    public function testList()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);

        $vertices = $this->createVertices(array(1 => $v1, 2 => $v2));

        $this->assertSame(array($v1, $v2), $vertices->getList());
    }
}
