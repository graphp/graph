<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\NegativeCycleException;
use Fhaculty\Graph\Algorithm\ShortestPath\MooreBellmanFord as SpMooreBellmanFord;

class DetectNegativeCycle extends Base
{
    /**
     *
     * @var Graph
     */
    private $graph;

    /**
     *
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

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

            return true;                                                      // cycle was found => okay
        } catch (UnderflowException $ignore) {}                                      // no cycle found

        return false;
    }

    /**
     * Searches all vertices for the first negative cycle
     *
     * @return Cycle
     * @throws UnderflowException if there's no negative cycle
     * @uses AlgorithmSpMooreBellmanFord::getVerticesId()
     */
    public function getCycleNegative()
    {
        $verticesVisited = array();                                            // remember vertices already visited, as they can not lead to a new cycle
        foreach ($this->graph->getVertices() as $vid => $vertex) {                // check for all vertices
            if (!isset($verticesVisited[$vid])) {                                // skip vertices already visited
                $alg = new SpMooreBellmanFord($vertex);                        // start MBF algorithm on current vertex

                try {
                    foreach ($alg->getVerticesId() as $vid) {                   // try to get all connected vertices (or throw new cycle)
                        $verticesVisited[$vid] = true;                         // getting connected vertices succeeded, so skip over all of them
                    }                                                           // no cycle found, check next vertex...
                } catch (NegativeCycleException $e) {                              // yey, negative cycle encountered => return

                    return $e->getCycle();
                }
            }
        }                                                                       // no more vertices to check => abort
        throw new UnderflowException('No negative cycle found');
    }

    /**
     * create new graph clone with only vertices and edges in negative cycle
     *
     * @return Graph
     * @throws Exception if there's no negative cycle
     * @uses AlgorithmDetectNegativeCycle::getCycleNegative()
     * @uses Cycle::createGraph()
     */
    public function createGraph()
    {
        return $this->getCycleNegative()->createGraph();
    }
}
