<?php

namespace Fhaculty\Graph\Edge;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Graph;

class UndirectedId extends Base
{
    /**
     * vertex ID of point a
     *
     * @var string
     */
    private $a;

    /**
     * Vertex ID of point b
     *
     * @var string
     */
    private $b;

    /**
     * parent graph
     *
     * @var Graph
     */
    private $graph;

    /**
     * instanciate new edges between two given vertices (MUST NOT BE CALLED MANUALLY!)
     *
     * @param Vertex $a
     * @param Vertex $b
     * @see Vertex::createEdge() to create undirected edges
     * @see Vertex::createEdgeTo() to create directed edges
     */
    public function __construct(Vertex $a, Vertex $b)
    {
        $this->a = $a->getId();
        $this->b = $b->getId();
        $this->graph = $a->getGraph();
    }

    public function getGraph()
    {
        return $this->graph;
    }

    public function getVerticesStart()
    {
        return array($this->graph->getVertex($this->a), $this->graph->getVertex($this->b));
    }

    public function getVerticesTarget()
    {
        return array($this->graph->getVertex($this->b), $this->graph->getVertex($this->a));
    }

    public function getVertices()
    {
        return new Vertices(array($this->graph->getVertex($this->a), $this->graph->getVertex($this->b)));
    }

    public function isLoop()
    {
        return ($this->a === $this->b);
    }

    public function isConnection(Vertex $from, Vertex $to)
    {
        if ($from->getGraph() !== $this->graph || $to->getGraph() !== $this->graph) {
            return false;
        }
        // one way                or                        other way
        return (($this->a === $from->getId() && $this->b === $to->getId()) || ($this->b === $from->getId() && $this->a === $to->getId()));
    }

    public function getVertexToFrom(Vertex $startVertex)
    {
        if ($startVertex->getGraph() === $this->graph) {
            if ($this->a === $startVertex->getId()) {
                return $this->graph->getVertex($this->b);
            } elseif ($this->b === $startVertex->getId()) {
                return $this->graph->getVertex($this->a);
            }
        }
        throw new InvalidArgumentException('Invalid start vertex');
    }

    public function getVertexFromTo(Vertex $endVertex)
    {
        if ($endVertex->getGraph() === $this->graph) {
            if ($this->a === $endVertex->getId()) {
                return $this->graph->getVertex($this->b);
            } elseif ($this->b === $endVertex->getId()) {
                return $this->graph->getVertex($this->a);
            }
        }
        throw new InvalidArgumentException('Invalid end vertex');
    }

    public function hasVertexStart(Vertex $startVertex)
    {
        if ($startVertex->getGraph() !== $this->graph) {
            return false;
        }

        return ($this->graph->getVertex($this->a) === $startVertex || $this->graph->getVertex($this->b) === $startVertex);
    }

    public function hasVertexTarget(Vertex $targetVertex)
    {
        // same implementation as direction does not matter
        return $this->hasVertexStart($targetVertex);
    }
}
