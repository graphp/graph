<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\NegativeCycleException;
use Fhaculty\Graph\Exception\UnderflowException;

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
class MooreBellmanFord extends Base
{
    /**
     * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
     *
     * @return Result
     * @throws NegativeCycleException if there is a negative cycle
     */
    public function createResult()
    {
        $alg = new SingleSource\MooreBellmanFord();

        return $alg->createResult($this->vertex);
    }

    /**
     * get negative cycle
     *
     * @return Walk
     * @throws UnderflowException if there's no negative cycle
     */
    public function getCycleNegative()
    {
        $alg = new SingleSource\MooreBellmanFord();

        return $alg->getCycleNegative($this->vertex);
    }
}
