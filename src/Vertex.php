<?php

namespace Graphp\Graph;

class Vertex extends Entity
{
    /**
     * @var Edge[]
     */
    private $edges = array();

    /**
     * @var Graph
     */
    private $graph;

    /**
     * Create a new Vertex
     *
     * @param Graph $graph      graph to be added to
     * @param array $attributes
     * @see Graph::createVertex() to create new vertices
     */
    public function __construct(Graph $graph, array $attributes = array())
    {
        $this->graph = $graph;
        $this->attributes = $attributes;

        $graph->addVertex($this);
    }

    /**
     * get graph this vertex is attached to
     *
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * add the given edge to list of connected edges (MUST NOT be called manually)
     *
     * @param  Edge                     $edge
     * @return void
     * @internal
     * @see Graph::createEdgeUndirected() instead!
     */
    public function addEdge(Edge $edge)
    {
        $this->edges[] = $edge;
    }

    /**
     * check whether this vertex has a direct edge to given $vertex
     *
     * @param  Vertex  $vertex
     * @return bool
     * @uses Edge::hasVertexTarget()
     */
    public function hasEdgeTo(Vertex $vertex)
    {
        foreach ($this->edges as $edge) {
            if ($edge->isConnection($this, $vertex)) {
                return true;
            }
        }

        return false;
    }

    /**
     * check whether the given vertex has a direct edge to THIS vertex
     *
     * @param  Vertex  $vertex
     * @return bool
     * @uses Vertex::hasEdgeTo()
     */
    public function hasEdgeFrom(Vertex $vertex)
    {
        return $vertex->hasEdgeTo($this);
    }

    /**
     * get list of ALL edges attached to this vertex
     *
     * @psalm-return list<Edge>
     * @return Edge[]
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * get list of all outgoing edges attached to this vertex
     *
     * @psalm-return list<Edge>
     * @return Edge[]
     */
    public function getEdgesOut()
    {
        $that = $this;
        $prev = null;

        return \array_values(\array_filter($this->edges, function (Edge $edge) use ($that, &$prev) {
            $ret = $edge->hasVertexStart($that);

            // skip duplicate directed loop edges
            if ($edge === $prev && $edge instanceof EdgeDirected) {
                $ret = false;
            }
            $prev = $edge;

            return $ret;
        }));
    }

    /**
     * get list of all ingoing edges attached to this vertex
     *
     * @psalm-return list<Edge>
     * @return Edge[]
     */
    public function getEdgesIn()
    {
        $that = $this;
        $prev = null;

        return \array_values(\array_filter($this->edges, function (Edge $edge) use ($that, &$prev) {
            $ret = $edge->hasVertexTarget($that);

            // skip duplicate directed loop edges
            if ($edge === $prev && $edge instanceof EdgeDirected) {
                $ret = false;
            }
            $prev = $edge;

            return $ret;
        }));
    }

    /**
     * get list of edges FROM this vertex TO the given vertex
     *
     * @param  Vertex $vertex
     * @psalm-return list<Edge>
     * @return Edge[]
     * @uses Edge::hasVertexTarget()
     */
    public function getEdgesTo(Vertex $vertex)
    {
        $that = $this;

        return \array_values(\array_filter($this->edges, function (Edge $edge) use ($that, $vertex) {
            return $edge->isConnection($that, $vertex);
        }));
    }

    /**
     * get list of edges FROM the given vertex TO this vertex
     *
     * @param  Vertex $vertex
     * @psalm-return list<Edge>
     * @return Edge[]
     * @uses Vertex::getEdgesTo()
     */
    public function getEdgesFrom(Vertex $vertex)
    {
        return $vertex->getEdgesTo($this);
    }

    /**
     * get list of adjacent vertices of this vertex (edge FROM or TO this vertex)
     *
     * If there are multiple parallel edges between the same Vertex, it will be
     * returned several times in the resulting list of vertices.
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     * @uses Edge::hasVertexStart()
     * @uses Edge::getVerticesToFrom()
     * @uses Edge::getVerticesFromTo()
     */
    public function getVerticesEdge()
    {
        $ret = array();
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexStart($this)) {
                $ret []= $edge->getVertexToFrom($this);
            } else {
                $ret []= $edge->getVertexFromTo($this);
            }
        }

        return $ret;
    }

    /**
     * get list of all vertices this vertex has an edge to
     *
     * If there are multiple parallel edges to the same Vertex, it will be
     * returned several times in the resulting list of vertices.
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     * @uses Vertex::getEdgesOut()
     * @uses Edge::getVerticesToFrom()
     */
    public function getVerticesEdgeTo()
    {
        $ret = array();
        foreach ($this->getEdgesOut() as $edge) {
            $ret []= $edge->getVertexToFrom($this);
        }

        return $ret;
    }

    /**
     * get list of all vertices that have an edge TO this vertex
     *
     * If there are multiple parallel edges from the same Vertex, it will be
     * returned several times in the resulting list of vertices.
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     * @uses Vertex::getEdgesIn()
     * @uses Edge::getVerticesFromTo()
     */
    public function getVerticesEdgeFrom()
    {
        $ret = array();
        foreach ($this->getEdgesIn() as $edge) {
            $ret []= $edge->getVertexFromTo($this);
        }

        return $ret;
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
