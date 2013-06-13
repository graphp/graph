<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

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

    /**
     * Calculates the flow for this Vertex: sum(outflow) - sum(inflow)
     *
     * Usually, vertices should have a resulting flow of 0: The sum of flows
     * entering a vertex must equal the sum of flows leaving a vertex. If the
     * resulting flow is < 0, this vertex is considered a sink (i.e. there's
     * more flow into this vertex). If the resulting flow is > 0, this vertex
     * is considered a "source" (i.e. there's more flow leaving this vertex).
     *
     * @param Vertex $vertex
     * @return float
     * @throws UnexpectedValueException if they are undirected edges
     * @see Vertex::getBalance()
     * @uses Vertex::getEdges()
     * @uses Edge::getFlow()
     */
    public function getFlowVertex(Vertex $vertex)
    {
        $sumOfFlow = 0;

        foreach ($vertex->getEdges() as $edge) {
            if (!($edge instanceof EdgeDirected)) {
                throw new UnexpectedValueException("TODO: undirected edges not suported yet");
            }

            // edge is an outgoing edge of this vertex
            if ($edge->hasVertexStart($vertex)) {
                // flowing out (flow is "pointing away")
                $sumOfFlow += $edge->getFlow();
                // this is an ingoing edge
            } else {
                // flowing in
                $sumOfFlow -= $edge->getFlow();
            }
        }

        return $sumOfFlow;
    }
}
