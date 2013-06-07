<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class Symmetric extends Base
{
    /**
     *
     * @var Graph
     */
    private $graph;

    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * checks whether this graph is symmetric (for every edge a->b there's also an edge b->a)
     *
     * @return boolean
     * @uses Graph::getEdges()
     * @uses EdgeDirected::getVertexStart()
     * @uses EdgeDirected::getVertedEnd()
     * @uses Vertex::hasEdgeTo()
     */
    public function isSymmetric()
    {
        // check all edges
        foreach ($this->graph->getEdges() as $edge) {
            // only check directed edges (undirected ones are symmetric by definition)
            if ($edge instanceof EdgeDirected) {
                // check if end also has an edge to start
                if (!$edge->getVertexEnd()->hasEdgeTo($edge->getVertexStart())) {
                    return false;
                }
            }
        }

        return true;
    }
}
