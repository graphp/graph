<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\NegativeCycleException;
use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\MooreBellmanFord;

class DetectNegativeCycle extends BaseGraph
{
    /**
     * check if the input graph has any negative cycles
     *
     * @return boolean
     * @uses AlgorithmDetectNegativeCycle::getCycleNegative()
     */
    public function hasCycleNegative()
    {
        try {
            $this->getCycleNegative();

            // cycle was found => okay
            return true;
        // no cycle found
        } catch (UnderflowException $ignore) {}

        return false;
    }

    /**
     * Searches all vertices for the first negative cycle
     *
     * @return Walk
     * @throws UnderflowException if there's no negative cycle
     * @uses AlgorithmSpMooreBellmanFord::getVertices()
     */
    public function getCycleNegative()
    {
        $alg = new MooreBellmanFord();

        // remember vertices already visited, as they can not lead to a new cycle
        $verticesVisited = array();
        // check for all vertices
        foreach ($this->graph->getVertices()->getMap() as $vid => $vertex) {
            // skip vertices already visited
            if (!isset($verticesVisited[$vid])) {
                try {
                    // start MBF algorithm on current vertex
                    // try to get all connected vertices (or throw new cycle)
                    foreach ($alg->createResult($vertex)->getVertices()->getIds() as $vid) {
                        // getting connected vertices succeeded, so skip over all of them
                        $verticesVisited[$vid] = true;
                    // no cycle found, check next vertex...
                    }
                // yey, negative cycle encountered => return
                } catch (NegativeCycleException $e) {
                    return $e->getCycle();
                }
            }
        // no more vertices to check => abort
        }
        throw new UnderflowException('No negative cycle found');
    }

    /**
     * create new graph clone with only vertices and edges in negative cycle
     *
     * @return Graph
     * @throws Exception if there's no negative cycle
     * @uses AlgorithmDetectNegativeCycle::getCycleNegative()
     * @uses Walk::createGraph()
     */
    public function createGraph()
    {
        return $this->getCycleNegative()->createGraph();
    }
}
