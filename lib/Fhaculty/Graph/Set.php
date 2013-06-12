<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Edge\Base as Edge;

/**
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 */
abstract class Set
{
    /**
     *
     * @var Edge[]
     */
    protected $edges = array();

    /**
     *
     * @var Vertex[]
     */
    protected $vertices = array();

    /**
     * returns an array of ALL Edges in this graph
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * returns an array of all Vertices
     *
     * @return Vertex[]
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * return number of vertices (aka. size of graph, |V| or just 'n')
     *
     * @return int
     */
    public function getNumberOfVertices()
    {
        return count($this->vertices);
    }

    /**
     * return number of edges
     *
     * @return int
     */
    public function getNumberOfEdges()
    {
        return count($this->edges);
    }

    /**
     * checks whether this graph has any loops (edges from vertex to itself)
     *
     * @return boolean
     * @uses Edge::isLoop()
     */
    public function hasLoop()
    {
        foreach ($this->edges as $edge) {
            if ($edge->isLoop()) {
                return true;
            }
        }

        return false;
    }
}
