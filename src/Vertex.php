<?php

namespace Graphp\Graph;

use Graphp\Graph\Attribute\AttributeAware;
use Graphp\Graph\Attribute\AttributeBagReference;
use Graphp\Graph\Exception\BadMethodCallException;
use Graphp\Graph\Exception\InvalidArgumentException;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\EdgesAggregate;
use Graphp\Graph\Set\Vertices;

class Vertex implements EdgesAggregate, AttributeAware
{
    /**
     * @var Edge[]
     */
    private $edges = array();

    /**
     * @var Graph
     */
    private $graph;

    private $attributes = array();

    /**
     * Create a new Vertex
     *
     * @param Graph $graph graph to be added to
     * @see Graph::createVertex() to create new vertices
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;

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
     * remove the given edge from list of connected edges (MUST NOT be called manually)
     *
     * @param  Edge                     $edge
     * @return void
     * @throws InvalidArgumentException if given edge does not exist
     * @internal
     * @see Edge::destroy() instead!
     */
    public function removeEdge(Edge $edge)
    {
        $id = \array_search($edge, $this->edges, true);
        if ($id === false) {
            throw new InvalidArgumentException('Given edge does NOT exist');
        }
        unset($this->edges[$id]);
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
        $that = $this;

        return $this->getEdges()->hasEdgeMatch(function (Edge $edge) use ($that, $vertex) {
            return $edge->isConnection($that, $vertex);
        });
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
     * get set of ALL Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdges()
    {
        return new Edges($this->edges);
    }

    /**
     * get set of all outgoing Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdgesOut()
    {
        $that = $this;
        $prev = null;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that, &$prev) {
            $ret = $edge->hasVertexStart($that);

            // skip duplicate directed loop edges
            if ($edge === $prev && $edge instanceof EdgeDirected) {
                $ret = false;
            }
            $prev = $edge;

            return $ret;
        });
    }

    /**
     * get set of all ingoing Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdgesIn()
    {
        $that = $this;
        $prev = null;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that, &$prev) {
            $ret = $edge->hasVertexTarget($that);

            // skip duplicate directed loop edges
            if ($edge === $prev && $edge instanceof EdgeDirected) {
                $ret = false;
            }
            $prev = $edge;

            return $ret;
        });
    }

    /**
     * get set of Edges FROM this vertex TO the given vertex
     *
     * @param  Vertex $vertex
     * @return Edges
     * @uses Edge::hasVertexTarget()
     */
    public function getEdgesTo(Vertex $vertex)
    {
        $that = $this;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that, $vertex) {
            return $edge->isConnection($that, $vertex);
        });
    }

    /**
     * get set of Edges FROM the given vertex TO this vertex
     *
     * @param  Vertex $vertex
     * @return Edges
     * @uses Vertex::getEdgesTo()
     */
    public function getEdgesFrom(Vertex $vertex)
    {
        return $vertex->getEdgesTo($this);
    }

    /**
     * get set of adjacent Vertices of this vertex (edge FROM or TO this vertex)
     *
     * If there are multiple parallel edges between the same Vertex, it will be
     * returned several times in the resulting Set of Vertices. If you only
     * want unique Vertex instances, use `getVerticesDistinct()`.
     *
     * @return Vertices
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

        return new Vertices($ret);
    }

    /**
     * get set of all Vertices this vertex has an edge to
     *
     * If there are multiple parallel edges to the same Vertex, it will be
     * returned several times in the resulting Set of Vertices. If you only
     * want unique Vertex instances, use `getVerticesDistinct()`.
     *
     * @return Vertices
     * @uses Vertex::getEdgesOut()
     * @uses Edge::getVerticesToFrom()
     */
    public function getVerticesEdgeTo()
    {
        $ret = array();
        foreach ($this->getEdgesOut() as $edge) {
            $ret []= $edge->getVertexToFrom($this);
        }

        return new Vertices($ret);
    }

    /**
     * get set of all Vertices that have an edge TO this vertex
     *
     * If there are multiple parallel edges from the same Vertex, it will be
     * returned several times in the resulting Set of Vertices. If you only
     * want unique Vertex instances, use `getVerticesDistinct()`.
     *
     * @return Vertices
     * @uses Vertex::getEdgesIn()
     * @uses Edge::getVerticesFromTo()
     */
    public function getVerticesEdgeFrom()
    {
        $ret = array();
        foreach ($this->getEdgesIn() as $edge) {
            $ret []= $edge->getVertexFromTo($this);
        }

        return new Vertices($ret);
    }

    /**
     * destroy vertex and all edges connected to it and remove reference from graph
     *
     * @uses Edge::destroy()
     * @uses Graph::removeVertex()
     */
    public function destroy()
    {
        foreach ($this->getEdges()->getEdgesDistinct() as $edge) {
            $edge->destroy();
        }
        $this->graph->removeVertex($this);
    }

    /**
     * do NOT allow cloning of objects
     *
     * @throws BadMethodCallException
     */
    private function __clone()
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException();
        // @codeCoverageIgnoreEnd
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }

    public function getAttributeBag()
    {
        return new AttributeBagReference($this->attributes);
    }
}
