<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Algorithm\Degree;

class Eulerian extends BaseGraph
{
    /**
     * check whether this graph has an eulerian cycle
     *
     * @return boolean
     * @uses Graph::isConnected()
     * @uses Degree::getDegreeVertex()
     * @todo isolated vertices should be ignored
     * @todo definition is only valid for undirected graphs
     */
    public function hasCycle()
    {
        if ($this->graph->isConnected()) {
            $alg = new Degree($this->graph);

            foreach ($this->graph->getVertices() as $vertex) {
                // uneven degree => fail
                if ($alg->getDegreeVertex($vertex) & 1) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
