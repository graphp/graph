<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use \SplPriorityQueue;

/**
 * Commonly used Dijkstra's shortest path algorithm
 *
 * This is asymptotically the fastest known single-source shortest-path
 * algorithm for arbitrary graphs with non-negative weights. If your Graph
 * contains an Edge with negative weight, if will throw an
 * UnexpectedValueException. Consider using (the slower) MooreBellmanFord
 * algorithm instead.
 *
 * @link http://en.wikipedia.org/wiki/Dijkstra%27s_algorithm
 * @see MooreBellmanFord
 */
class Dijkstra extends Base
{
    /**
     * get all edges on shortest path for this vertex
     *
     * @return Result
     * @throws UnexpectedValueException when encountering an Edge with negative weight
     */
    public function createResult()
    {
        $alg = new SingleSource\Dijkstra();

        return $alg->createResult($this->vertex);
    }
}
