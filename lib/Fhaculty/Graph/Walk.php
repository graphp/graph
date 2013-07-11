<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\EdgesAggregate;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\VerticesAggregate;

/**
 * Base Walk class
 *
 * The general term "Walk" bundles the following mathematical concepts:
 * walk, path, cycle, circuit, loop, trail, tour, etc.
 *
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 * @see Fhaculty\Graph\Algorithm\Property\WalkProperty for checking special cases, such as cycles, loops, closed trails, etc.
 */
class Walk extends Set implements VerticesAggregate, EdgesAggregate
{
    /**
     * construct new walk from given start vertex and given array of edges
     *
     * @param  Edges|Edge[]         $edges
     * @param  Vertex               $startVertex
     * @return \Fhaculty\Graph\Walk
     */
    public static function factoryFromEdges($edges, Vertex $startVertex)
    {
        $vertices = array($startVertex);
        $vertexCurrent = $startVertex;
        foreach ($edges as $edge) {
            $vertexCurrent = $edge->getVertexToFrom($vertexCurrent);
            $vertices []= $vertexCurrent;
        }

        return new self($vertices, $edges);
    }

    /**
     *
     * @var Vertices
     */
    protected $vertices;

    /**
     *
     * @var Edges
     */
    protected $edges;

    protected function __construct($vertices, $edges)
    {
        $this->vertices = Vertices::factory($vertices);
        $this->edges    = Edges::factory($edges);
    }

    /**
     * return original graph
     *
     * @return Graph
     * @uses Walk::getVertexSource()
     * @uses Vertex::getGraph()
     */
    public function getGraph()
    {
        return $this->getVertexSource()->getGraph();
    }

    /**
     * create new graph clone with only vertices and edges actually in the walk
     *
     * do not add duplicate vertices and edges for loops and intersections, etc.
     *
     * @return Graph
     * @uses Walk::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        // create new graph clone with only edges of walk
        $graph = $this->getGraph()->createGraphCloneEdges($this->getEdges());
        $vertices = $this->getVertices()->getMap();
        // get all vertices
        foreach ($graph->getVertices()->getMap() as $vid => $vertex) {
            if (!isset($vertices[$vid])) {
                // remove those not present in the walk (isolated vertices, etc.)
                $vertex->destroy();
            }
        }

        return $graph;
    }

    /**
     * return set of all Edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * If you need to return set a of all unique Edges of walk, use
     * `Walk::getEdges()->getEdgesDistinct()` instead.
     *
     * @return Edges
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * return set of all Vertices of walk (in sequence visited in walk, may contain duplicates)
     *
     * If you need to return set a of all unique Vertices of walk, use
     * `Walk::getVertices()->getVerticesDistinct()` instead.
     *
     * @return Vertices
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * return source vertex (first vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexSource()
    {
        return $this->vertices->getVertexFirst();
    }

    /**
     * return target vertex (last vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexTarget()
    {
        return $this->vertices->getVertexLast();
    }

    /**
     * get alternating sequence of vertex, edge, vertex, edge, ..., vertex
     *
     * @return array
     */
    public function getAlternatingSequence()
    {
        $edges    = $this->edges->getVector();
        $vertices = $this->vertices->getVector();

        $ret = array();
        for ($i = 0, $l = count($this->edges); $i < $l; ++$i) {
            $ret []= $vertices[$i];
            $ret []= $edges[$i];
        }
        $ret[] = $vertices[$i];

        return $ret;
    }

    /**
     * check to make sure this walk is still valid (i.e. source graph still contains all vertices and edges)
     *
     * @return boolean
     * @uses Walk::getGraph()
     * @uses Graph::getVertices()
     * @uses Graph::getEdges()
     */
    public function isValid()
    {
        $vertices = $this->getGraph()->getVertices()->getMap();
        // check source graph contains all vertices
        foreach ($this->getVertices()->getMap() as $vid => $vertex) {
            // make sure vertex ID exists and has not been replaced
            if (!isset($vertices[$vid]) || $vertices[$id] !== $vertex) {
                return false;
            }
        }
        $edges = $this->getGraph()->getEdges();
        // check source graph contains all edges
        foreach ($this->edges as $edge) {
            if (!in_array($edge, $edges, true)) {
                return false;
            }
        }

        return true;
    }
}
