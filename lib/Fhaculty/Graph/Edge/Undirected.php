<?php

namespace Fhaculty\Graph\Edge;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;

class Undirected extends Base
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
     * create new undirected edge between given vertices (MUST NOT BE CALLED MANUALLY!)
     *
     * @param Vertex $a
     * @param Vertex $b
     * @deprecated obsoleted by UndirectedId
     * @see Vertex::createEdge() instead
     * @see UndirectedId for current replacement implementation
     */
    public function __construct(Vertex $a, Vertex $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function getVerticesTarget()
    {
        return array($this->b, $this->a);
    }

    public function getVerticesStart()
    {
        return  array($this->a, $this->b);
    }

    public function getVertices()
    {
        return array($this->a, $this->b);
    }

    public function isConnection(Vertex $from, Vertex $to)
    {
        //                              one way                or                        other way
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
            throw new InvalidArgumentException('Invalid start vertex');
        }
    }

    public function getVertexFromTo(Vertex $endVertex)
    {
        if ($this->a === $endVertex) {
            return $this->b;
        } elseif ($this->b === $endVertex) {
            return $this->a;
        } else {
            throw new InvalidArgumentException('Invalid end vertex');
        }
    }

    public function hasVertexStart(Vertex $startVertex)
    {
        return ($this->a === $startVertex || $this->b === $startVertex);
    }

    public function hasVertexTarget(Vertex $targetVertex)
    {
        return $this->hasVertexStart($targetVertex); // same implementation as direction does not matter
    }
}
