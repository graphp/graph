<?php

namespace Graphp\Graph;

/**
 * Abstract base for `EdgeUndirected` and `EdgeDirected` containing common interfaces and behavior for all edges.
 *
 * @see EdgeUndirected
 * @see EdgeDirected
 */
abstract class Edge extends Entity
{
    protected $attributes = array();

    /**
     * get vertices that are a target of this edge
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     */
    abstract public function getVerticesTarget();

    /**
     * get vertices that are the start of this edge
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     */
    abstract public function getVerticesStart();

    /**
     * return true if this edge is an outgoing edge of the given vertex (i.e. the given vertex is a valid start vertex of this edge)
     *
     * @param  Vertex $startVertex
     * @return bool
     * @uses Vertex::getVertexToFrom()
     */
    abstract public function hasVertexStart(Vertex $startVertex);

    /**
     * return true if this edge is an ingoing edge of the given vertex (i . e. the given vertex is a valid end vertex of this edge)
     *
     * @param  Vertex $targetVertex
     * @return bool
     * @uses Vertex::getVertexFromTo()
     */
    abstract function hasVertexTarget(Vertex $targetVertex);

    abstract public function isConnection(Vertex $from, Vertex $to);

    /**
     * returns whether this edge is actually a loop
     *
     * @return bool
     */
    abstract public function isLoop();

    /**
     * get target vertex we can reach with this edge from the given start vertex
     *
     * @param  Vertex                    $startVertex
     * @return Vertex
     * @throws \InvalidArgumentException if given $startVertex is not a valid start
     * @see self::hasEdgeFrom() to check if given start is valid
     */
    abstract public function getVertexToFrom(Vertex $startVertex);

    /**
     * get start vertex which can reach us(the given end vertex) with this edge
     *
     * @param  Vertex                    $endVertex
     * @return Vertex
     * @throws \InvalidArgumentException if given $startVertex is not a valid end
     * @see self::hasEdgeFrom() to check if given start is valid
     */
    abstract public function getVertexFromTo(Vertex $endVertex);

    /**
     * get list of all vertices this edge connects
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     */
    abstract public function getVertices();

    /**
     * get graph instance this edge is attached to
     *
     * @return Graph
     */
    public function getGraph()
    {
        $vertices = $this->getVertices();
        $vertex = \reset($vertices);
        \assert($vertex instanceof Vertex);

        return $vertex->getGraph();
    }

    /**
     * do NOT allow cloning of objects
     *
     * @throws \BadMethodCallException
     * @codeCoverageIgnore
     */
    private function __clone()
    {
        throw new \BadMethodCallException();
    }
}
