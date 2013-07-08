<?php

namespace Fhaculty\Graph;

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
class Walk extends Set
{
    /**
     * construct new walk from given start vertex and given array of edges
     *
     * @param  array                $edges
     * @param  Vertex               $startVertex
     * @return \Fhaculty\Graph\Walk
     */
    public static function factoryFromEdges(array $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex);
        $vertexCurrent = $startVertex;
        foreach ($edges as $edge) {
            $vertexCurrent = $edge->getVertexToFrom($vertexCurrent);
            $vertices []= $vertexCurrent;
        }

        return new self($vertices, $edges);
    }

    protected function __construct(array $vertices, array $edges)
    {
        $this->vertices = $vertices;
        $this->edges    = $edges;
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
        $vertices = $this->getVertices();
        // get all vertices
        foreach ($graph->getVertices() as $vid => $vertex) {
            if (!isset($vertices[$vid])) {
                // remove those not present in the walk (isolated vertices, etc.)
                $vertex->destroy();
            }
        }

        return $graph;
    }

    /**
     * return array of all unique edges of walk
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        $edges = array();
        foreach ($this->edges as $edge) {
            // filter duplicate edges
            if (!in_array($edge, $edges, true)) {
                $edges []= $edge;
            }
        }

        return $edges;
    }

    /**
     * return array/list of all edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return Edge[]
     */
    public function getEdgesSequence()
    {
        return $this->edges;
    }

    /**
     * return array of all unique vertices of walk
     *
     * @return Vertex[]
     */
    public function getVertices()
    {
        $vertices = array();
        foreach ($this->vertices as $vertex) {
            $vertices[$vertex->getId()] = $vertex;
        }

        return $vertices;
    }

    /**
     * return array/list of all vertices of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return Vertex[]
     */
    public function getVerticesSequence()
    {
        return $this->vertices;
    }

    /**
     * return array of all vertex ids of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return string[]
     * @uses Vertex::getId()
     */
    public function getVerticesSequenceId()
    {
        $ids = array();
        foreach ($this->vertices as $vertex) {
            $ids []= $vertex->getId();
        }

        return $ids;
    }

    /**
     * get IDs of all vertices in the walk
     *
     * @return int[]
     */
    public function getVerticesId()
    {
        return array_keys($this->getVertices());
    }

    /**
     * return source vertex (first vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexSource()
    {
        return reset($this->vertices);
    }

    /**
     * return target vertex (last vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexTarget()
    {
        return end($this->vertices);
    }

    /**
     * get alternating sequence of vertex, edge, vertex, edge, ..., vertex
     *
     * @return array
     */
    public function getAlternatingSequence()
    {
        $ret = array();
        for ($i = 0, $l = count($this->edges); $i < $l; ++$i) {
            $ret []= $this->vertices[$i];
            $ret []= $this->edges[$i];
        }
        $ret[] = $this->vertices[$i];

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
        $vertices = $this->getGraph()->getVertices();
        // check source graph contains all vertices
        foreach ($this->vertices as $vertex) {
            $vid = $vertex->getId();
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
