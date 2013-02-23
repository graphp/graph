<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Graph;

class Eulerian extends Base
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
     * check whether this graph has an eulerian cycle
     *
     * @return boolean
     * @uses Graph::isConnected()
     * @uses Vertex::getDegree()
     * @todo isolated vertices should be ignored
     * @todo definition is only valid for undirected graphs
     */
    public function hasCycle()
    {
        if ($this->graph->isConnected()) {
            foreach ($this->graph->getVertices() as $vertex) {
                // uneven degree => fail
                if ($vertex->getDegree() & 1) {

                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
