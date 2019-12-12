<?php

namespace Graphp\Graph\Tests;

class EdgeUndirectedTest extends EdgeBaseTest
{
    protected function createEdge(array $attributes = array())
    {
        // 1 -- 2
        return $this->graph->createEdgeUndirected($this->v1, $this->v2, $attributes);
    }

    protected function createEdgeLoop()
    {
        // 1 --\
        // |   |
        // \---/
        return $this->graph->createEdgeUndirected($this->v1, $this->v1);
    }

    public function testVerticesEnds()
    {
        $this->assertEquals(array($this->v1, $this->v2), $this->edge->getVerticesStart()->getVector());
        $this->assertEquals(array($this->v2, $this->v1), $this->edge->getVerticesTarget()->getVector());
    }
}
