<?php

namespace Graphp\Graph\Tests;

class EdgeDirectedTest extends EdgeBaseTest
{
    protected function createEdgeUndirected()
    {
        // 1 -> 2
        return $this->graph->createEdgeDirected($this->v1, $this->v2);
    }

    protected function createEdgeLoop()
    {
        // 1 --\
        // ^   |
        // \---/
        return $this->graph->createEdgeDirected($this->v1, $this->v1);
    }

    public function testVerticesEnds()
    {
        $this->assertEquals(array($this->v1), $this->edge->getVerticesStart()->getVector());
        $this->assertEquals(array($this->v2), $this->edge->getVerticesTarget()->getVector());
    }
}