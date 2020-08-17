<?php

namespace Graphp\Graph\Tests;

use Graphp\Graph\Edge;
use Graphp\Graph\Graph;
use Graphp\Graph\Vertex;

abstract class EdgeTest extends EntityTest
{
    /**
     * @var Graph
     */
    protected $graph;

    /**
     * @var Vertex
     */
    protected $v1;

    /**
     * @var Vertex
     */
    protected $v2;

    /**
     * @var Edge
     */
    protected $edge;

    abstract protected function createEdge(array $attributes = array());

    /**
     * @return Edge
     */
    abstract protected function createEdgeLoop();

    /**
     * @before
     */
    public function setUpGraphAndEdge()
    {
        $this->graph = new Graph();
        $this->v1 = $this->graph->createVertex();
        $this->v2 = $this->graph->createVertex();

        $this->edge = $this->createEdge();
    }

    public function testEdgeConstructorDefaultHasNoAttributes()
    {
        $this->assertNull($this->edge->getAttribute('hello'));
        $this->assertEquals('default', $this->edge->getAttribute('hello', 'default'));
        $this->assertEquals(array(), $this->edge->getAttributes());
    }

    public function testEdgeConstructorWithAttributeReturnsAttributes()
    {
        $edge = $this->createEdge(array('hello' => 'wörld'));
        $this->assertEquals('wörld', $edge->getAttribute('hello'));
        $this->assertEquals(array('hello' => 'wörld'), $edge->getAttributes());
    }

    public function testEdgeVertices()
    {
        $this->assertEquals(array($this->v1, $this->v2), $this->edge->getVertices());

        $this->assertSame($this->graph, $this->edge->getGraph());
    }

    public function testEdgeStartVertex()
    {
        $this->assertTrue($this->edge->hasVertexStart($this->v1));
        $this->assertTrue($this->edge->hasVertexTarget($this->v2));

        $v3 = $this->graph->createVertex();

        $this->assertFalse($this->edge->hasVertexStart($v3));
        $this->assertFalse($this->edge->hasVertexTarget($v3));
    }

    public function testEdgeFromInvalid()
    {
        $v3 = $this->graph->createVertex();

        $this->setExpectedException('InvalidArgumentException');
        $this->edge->getVertexFromTo($v3);
    }

    public function testEdgeToInvalid()
    {
        $v3 = $this->graph->createVertex();

        $this->setExpectedException('InvalidArgumentException');
        $this->edge->getVertexToFrom($v3);
    }

    public function testLoop()
    {
        $edge = $this->createEdgeLoop();

        $this->assertTrue($edge->isLoop());
        $this->assertEquals(array($this->v1, $this->v1), $edge->getVertices());
        $this->assertSame($this->v1, $edge->getVertexFromTo($this->v1));
        $this->assertSame($this->v1, $edge->getVertexToFrom($this->v1));
    }

    protected function createEntity()
    {
        return $this->createEdge();
    }
}
