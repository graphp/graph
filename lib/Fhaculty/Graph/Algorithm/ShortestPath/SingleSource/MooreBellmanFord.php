<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\NegativeCycleException;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Vertex;

/**
 * Moore-Bellman-Ford's shortest path algorithm
 *
 * It is slower than Dijkstra's algorithm for the same problem, but more
 * versatile, as it is capable of handling Graphs with negative Edge weights.
 *
 * Also known as just "Bellman–Ford algorithm".
 *
 * @link http://en.wikipedia.org/wiki/Bellman%E2%80%93Ford_algorithm
 */
class MooreBellmanFord implements Base
{
    /**
     * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
     *
     * @return Result
     * @throws NegativeCycleException if there is a negative cycle
     */
    public function createResult(Vertex $startVertex)
    {
        return new ResultFromVertexPredecessors($startVertex, $this->getPredecessorMap($startVertex));
    }

    /**
     * get negative cycle
     *
     * @return Walk
     * @throws UnderflowException if there's no negative cycle
     */
    public function getCycleNegative(Vertex $startVertex)
    {
        try {
            $this->getPredecessorMap($startVertex);
        } catch (NegativeCycleException $e) {
            return $e->getCycle();
        }
        throw new UnderflowException('No cycle found');
    }

    /**
     *
     *
     * @param Edges    $edges
     * @param int[]    $totalCostOfCheapestPathTo
     * @param Vertex[] $predecessorVertexOfCheapestPathTo
     *
     * @return Vertex|NULL
     */
    private function bigStep(Edges $edges, array &$totalCostOfCheapestPathTo, array &$predecessorVertexOfCheapestPathTo)
    {
        $changed = NULL;
        // check for all edges
        foreach ($edges as $edge) {
            /* @var $edge Edge */
            // check for all "ends" of this edge (or for all targets)
            foreach ($edge->getVerticesTarget() as $toVertex) {
                /* @var $toVertex Vertex */
                $fromVertex = $edge->getVertexFromTo($toVertex);

                // If the fromVertex already has a path
                if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])) {
                    // New possible costs of this path
                    $newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight();
                    if (is_infinite($newCost)) {
                        $newCost = $edge->getWeight() + 0;
                    }

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

    private function getPredecessorMap(Vertex $startVertex)
    {
        // start node distance, add placeholder weight
        $totalCostOfCheapestPathTo  = array($startVertex->getId() => INF);

        // predecessor
        $predecessorVertexOfCheapestPathTo  = array($startVertex->getId() => $startVertex);

        // the usal algorithm says we repeat (n-1) times.
        // but because we also want to check for loop edges on the start vertex,
        // we have to add an additional step:
        $numSteps = count($startVertex->getGraph()->getVertices());
        $edges = $startVertex->getGraph()->getEdges();
        $changed = true;

        for ($i = 0; $i < $numSteps && $changed; ++$i) {
            $changed = $this->bigStep($edges, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo);
        }

        // no cheaper edge to start vertex found => remove placeholder weight
        if ($totalCostOfCheapestPathTo[$startVertex->getId()] === INF) {
            unset($predecessorVertexOfCheapestPathTo[$startVertex->getId()]);
        }

        // Check for negative cycles (only if last step didn't already finish anyway)
        // something is still changing...
        if ($changed && $changed = $this->bigStep($edges, $totalCostOfCheapestPathTo, $predecessorVertexOfCheapestPathTo)) {
            $cycle = Walk::factoryCycleFromPredecessorMap($predecessorVertexOfCheapestPathTo, $changed, Edges::ORDER_WEIGHT);
            throw new NegativeCycleException('Negative cycle found', 0, NULL, $cycle);
        }

        return $predecessorVertexOfCheapestPathTo;
    }
}
