<?php

namespace Graphp\Graph;

class EdgeUndirected extends Edge
{
    /**
     * vertex a
     *
     * @var Vertex
     */
    private $a;

    /**
     * vertex b
     *
     * @var Vertex
     */
    private $b;

    /**
     * [Internal] Create a new undirected edge between given vertices
     *
     * @param Vertex $a
     * @param Vertex $b
     * @param array  $attributes
     * @see Graph::createEdgeUndirected() to create undirected edges
     * @see Graph::createEdgeDirected() to create directed edges
     * @internal
     */
    public function __construct(Vertex $a, Vertex $b, array $attributes = array())
    {
        if ($a->getGraph() !== $b->getGraph()) {
            throw new \InvalidArgumentException('Vertices have to be within the same graph');
        }

        $this->a = $a;
        $this->b = $b;
        $this->attributes = $attributes;

        $a->getGraph()->addEdge($this);
        $a->addEdge($this);
        $b->addEdge($this);
    }

    public function getVerticesTarget()
    {
        return array($this->b, $this->a);
    }

    public function getVerticesStart()
    {
        return array($this->a, $this->b);
    }

    public function getVertices()
    {
        return array($this->a, $this->b);
    }

    public function isConnection(Vertex $from, Vertex $to)
    {
        // one way                or                        other way
        return (($this->a === $from && $this->b === $to) || ($this->b === $from && $this->a === $to));
    }

    public function isLoop()
    {
        return ($this->a === $this->b);
    }

    public function getVertexToFrom(Vertex $startVertex)
    {
        if ($this->a === $startVertex) {
            return $this->b;
        } elseif ($this->b === $startVertex) {
            return $this->a;
        } else {
            throw new \InvalidArgumentException('Invalid start vertex');
        }
    }

    public function getVertexFromTo(Vertex $endVertex)
    {
        if ($this->a === $endVertex) {
            return $this->b;
        } elseif ($this->b === $endVertex) {
            return $this->a;
        } else {
            throw new \InvalidArgumentException('Invalid end vertex');
        }
    }

    public function hasVertexStart(Vertex $startVertex)
    {
        return ($this->a === $startVertex || $this->b === $startVertex);
    }

    public function hasVertexTarget(Vertex $targetVertex)
    {
        // same implementation as direction does not matter
        return $this->hasVertexStart($targetVertex);
    }
}
