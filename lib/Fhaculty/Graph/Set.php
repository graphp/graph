<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\EdgesAggregate;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\VerticesAggregate;

/**
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 */
abstract class Set implements VerticesAggregate, EdgesAggregate
{
    /**
     * returns a set of ALL Edges in this graph
     *
     * @return Edges
     */
    // abstract public function getEdges();

    /**
     * returns a set of all Vertices
     *
     * @return Vertices
     */
    // abstract public function getVertices();

    /**
     * return number of vertices (aka. size of graph, |V| or just 'n')
     *
     * @return int
     */
    public function getNumberOfVertices()
    {
        return count($this->getVertices());
    }

    /**
     * return number of edges
     *
     * @return int
     */
    public function getNumberOfEdges()
    {
        return count($this->getEdges());
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
