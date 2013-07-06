<?php

namespace Fhaculty\Graph\Edge;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\LogicException;
use Fhaculty\Graph\Vertex;

class Directed extends Base
{
    /**
     * source/start vertex
     *
     * @var Vertex
     */
    private $from;

    /**
     * target/end vertex
     *
     * @var Vertex
     */
    private $to;

    /**
     * creats a new Edge (MUST NOT BE CALLED MANUALLY!)
     *
     * @param Vertex $from start/source Vertex
     * @param Vertex $to   end/target Vertex
     * @see Vertex::createEdgeTo() to create directed edges
     * @see Vertex::createEdge() to create undirected edges
     */
    public function __construct(Vertex $from, Vertex $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getVerticesTarget()
    {
        return array($this->to);
    }

    public function getVerticesStart()
    {
        return array($this->from);
    }

    public function getVertices()
    {
        return array($this->from, $this->to);
    }

    /**
     * get end/target vertex
     *
     * @return Vertex
     */
    public function getVertexEnd()
    {
        return $this->to;
    }

    /**
     * get start vertex
     *
     * @return Vertex
     */
    public function getVertexStart()
    {
        return $this->from;
    }

    public function isConnection(Vertex $from, Vertex $to)
    {
        return ($this->to === $to && $this->from === $from);
    }

    public function isLoop()
    {
        return ($this->to === $this->from);
    }

    public function getVertexToFrom(Vertex $startVertex)
    {
        if ($this->from !== $startVertex) {
            throw new InvalidArgumentException('Invalid start vertex');
        }

        return $this->to;
    }

    public function getVertexFromTo(Vertex $endVertex)
    {
        if ($this->to !== $endVertex) {
            throw new InvalidArgumentException('Invalid end vertex');
        }

        return $this->from;
    }

    public function hasVertexStart(Vertex $startVertex)
    {
        return ($this->from === $startVertex);
    }

    public function hasVertexTarget(Vertex $targetVertex)
    {
        return ($this->to === $targetVertex);
    }
}
