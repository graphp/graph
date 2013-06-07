<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Cycle;
use Fhaculty\Graph\Exception\NegativeCycleException;

class MooreBellmanFord extends Base
{
    /**
     *
     *
     * @param Edge[]   $edges
     * @param int[]    $totalCostOfCheapestPathTo
     * @param Vertex[] $predecessorVertexOfCheapestPathTo
     *
     * @return Vertex|NULL
     */
    private function bigStep(array &$edges, array &$totalCostOfCheapestPathTo, array &$predecessorVertexOfCheapestPathTo)
    {
        $changed = NULL;
        // check for all edges
        foreach ($edges as $edge) {
            // check for all "ends" of this edge (or for all targetes)
            foreach ($edge->getVerticesTarget() as $toVertex) {
                $fromVertex = $edge->getVertexFromTo($toVertex);

                // If the fromVertex already has a path
                if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])) {
                    // New possible costs of this path
                    $newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight();

                    // No path has been found yet
                    if (!isset($totalCostOfCheapestPathTo[$toVertex->getId()])
                            // OR this path is cheaper than the old path
                            || $totalCostOfCheapestPathTo[$toVertex->getId()] > $newCost){

                        $changed = $toVertex;
                        $totalCostOfCheapestPathTo[$toVertex->getId()] = $newCost;
                        $predecessorVertexOfCheapestPathTo[$toVertex->getId()] = $fromVertex;
                    }
                }
            }
        }

        return $changed;
    }

    /**
     * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
     *
     * @return Edge[]
     * @throws NegativeCycleException if there is a negative cycle
     */
    public function getEdges()
    {
        // start node distance
        $totalCostOfCheapestPathTo  = array($this->vertex->getId() => 0);

        // predecessor
        $predecessorVertexOfCheapestPathTo  = array($this->vertex->getId() => $this->vertex);

        // repeat (n-1) times
        $numSteps = $this->vertex->getGraph()->getNumberOfVertices() - 1;
        $edges = $this->vertex->getGraph()->getEdges();
        $changed = true;
        // repeat n-1 times
        for ($i = 0; $i < $numSteps && $changed; ++$i) {
            $changed = $this->bigStep($edges, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo);
        }

        // algorithm is done, build graph
        $returnEdges = $this->getEdgesCheapestPredecesor($predecessorVertexOfCheapestPathTo);

        // Check for negative cycles (only if last step didn't already finish anyway)
        // something is still changing...
        if ($changed && $changed = $this->bigStep($edges, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo)) {
            $cycle = Cycle::factoryFromPredecessorMap($predecessorVertexOfCheapestPathTo, $changed, Edge::ORDER_WEIGHT);
            throw new NegativeCycleException('Negative cycle found', 0, NULL, $cycle);
        }

        return $returnEdges;
    }

    /**
     * get negative cycle
     *
     * @return Cycle
     * @throws UnderflowException if there's no negative cycle
     */
    public function getCycleNegative()
    {
        try {
            $this->getEdges();
        } catch (NegativeCycleException $e) {
            return $e->getCycle();
        }
        throw new UnderflowException('No cycle found');
    }
}
