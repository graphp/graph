<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\Base;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class Balance extends Base
{
    private $graph;

    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    public function getBalance()
    {
        $balance = 0;
        // Sum for all vertices of value
        foreach ($this->graph->getVertices() as $vertex) {
            $balance += $vertex->getBalance();
        }

        return $balance;
    }

    /**
     * check if the current flow is balanced (aka "balanced flow" or "b-flow")
     *
     * a flow is considered balanced if each edge's current flow does not exceed its
     * maximum capacity (which is always guaranteed due to the implementation
     * of Edge::setFlow()) and each vertices' flow (i.e. outflow-inflow) equals
     * its balance.
     *
     * checking whether the FLOW is balanced is not to be confused with checking
     * whether the GRAPH is balanced (see Graph::isBalanced() instead)
     *
     * @return boolean
     * @see Graph::isBalanced() if you merely want to check indegree=outdegree
     * @uses Vertex::getFlow()
     * @uses Vertex::getBalance()
     */
    public function isBalancedFlow()
    {
        // no need to check for each edge: flow <= capacity (setters already check that)
        // check for each vertex: outflow-inflow = balance
        foreach ($this->graph->getVertices() as $vertex) {
            if ($vertex->getFlow() === $vertex->getBalance()) {
                return false;
            }
        }

        return true;
    }
}
