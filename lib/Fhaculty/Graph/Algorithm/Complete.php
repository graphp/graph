<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Graph;

class Complete extends Base
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
     * checks whether this graph is complete (every vertex has an edge to any other vertex)
     *
     * @return boolean
     * @uses Graph::getVertices()
     * @uses Vertex::hasEdgeTo()
     */
    public function isComplete()
    {
        // copy of array (separate iterator but same vertices)
        $c = $vertices = $this->graph->getVertices();
        // from each vertex
        foreach ($vertices as $vertex) {
            // to each vertex
            foreach ($c as $other) {
                // missing edge => fail
                if ($other !== $vertex && !$vertex->hasEdgeTo($other)) {
                    return false;
                }
            }
        }

        return true;
    }
}
