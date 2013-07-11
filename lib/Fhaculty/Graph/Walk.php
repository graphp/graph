<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Exception\UnderflowException;

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
     * @return Walk
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

    /**
     * create new cycle instance from given predecessor map
     *
     * @param  Vertex[]           $predecessors map of vid => predecessor vertex instance
     * @param  Vertex             $vertex       start vertex to search predecessors from
     * @param  int                $by
     * @param  boolean            $desc
     * @return Walk
     * @throws UnderflowException
     * @see Edge::getFirst() for parameters $by and $desc
     * @uses self::factoryFromVertices()
     */
    public static function factoryCycleFromPredecessorMap($predecessors, $vertex, $by = Edge::ORDER_FIFO, $desc = false)
    {
        /*$checked = array();
         foreach ($predecessors as $vertex) {
        $vid = $vertex->getId();
        if (!isset($checked[$vid])) {

        }
        }*/

        // find a vertex in the cycle
        $vid = $vertex->getId();
        $startVertices = array();
        do {
            $startVertices[$vid] = $vertex;

            $vertex = $predecessors[$vid];
            $vid = $vertex->getId();
        } while (!isset($startVertices[$vid]));

        // find negative cycle
        $vid = $vertex->getId();
        // build array of vertices in cycle
        $vertices = array();
        do {
            // add new vertex to cycle
            $vertices[$vid] = $vertex;

            // get predecessor of vertex
            $vertex = $predecessors[$vid];
            $vid = $vertex->getId();
            // continue until we find a vertex that's already in the circle (i.e. circle is closed)
        } while (!isset($vertices[$vid]));

        // reverse cycle, because cycle is actually built in opposite direction due to checking predecessors
        $vertices = array_reverse($vertices, true);

        return self::factoryCycleFromVertices($vertices, $by, $desc);
    }

    /**
     * create new cycle instance with edges between given vertices
     *
     * @param  Vertex[]           $vertices
     * @param  int                $by
     * @param  boolean            $desc
     * @return Walk
     * @throws UnderflowException if no vertices were given
     * @see Edge::getFirst() for parameters $by and $desc
     */
    public static function factoryCycleFromVertices($vertices, $by = Edge::ORDER_FIFO, $desc = false)
    {
        $edges = array();
        $first = NULL;
        $last = NULL;
        foreach ($vertices as $vertex) {
            // skip first vertex as last is unknown
            if ($first === NULL) {
                $first = $vertex;
            } else {
                // pick edge between last vertex and this vertex
                $edges []= Edge::getFirst($last->getEdgesTo($vertex), $by, $desc);
            }
            $last = $vertex;
        }
        if ($last === NULL) {
            throw new UnderflowException('No vertices given');
        }
        // additional edge from last vertex to first vertex
        $edges []= Edge::getFirst($last->getEdgesTo($first), $by, $desc);

        return new self($vertices, $edges);
    }

    /**
     * create new cycle instance with vertices connected by given edges
     *
     * @param  Edge[] $edges
     * @param  Vertex $startVertex
     * @return Walk
     */
    public static function factoryCycleFromEdges(array $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex->getId() => $startVertex);
        foreach ($edges as $edge) {
            $vertex = $edge->getVertexToFrom($startVertex);
            $vertices[$vertex->getId()] = $vertex;
            $startVertex = $vertex;
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
