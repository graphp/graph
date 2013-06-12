<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Graph;

/**
 * Basic algorithms for working with flow graphs
 *
 * A flow network (also known as a transportation network) is a directed graph
 * where each edge has a capacity and each edge receives a flow.
 *
 * @link http://en.wikipedia.org/wiki/Flow_network
 * @see Algorithm\Balance
 */
class Flow extends BaseSet
{
    /**
     * check if this graph has any flow set (any edge has a non-NULL flow)
     *
     * @return boolean
     * @uses Edge::getFlow()
     */
    public function hasFlow()
    {
        foreach ($this->set->getEdges() as $edge) {
            if ($edge->getFlow() !== NULL) {
                return true;
            }
        }

        return false;
    }
}
