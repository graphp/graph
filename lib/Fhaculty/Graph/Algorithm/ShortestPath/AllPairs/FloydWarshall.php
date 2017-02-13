<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\AllPairs;

use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Vertex;

/**
 * Class FloydWarshall
 *
 * An implementation of the Floyd Warshall algorithm to find the all-pairs
 * shortest path, with positive and negative weights. The original algorithm
 * calculates the shortest distances between each pair of edges without giving
 * information about the path itself, this implementation , however, returns
 * a list of edges representing the shortest path for each pair [A, B], being
 * A and B the origin and destination vertices respectively.
 *
 * If there is no path between two vertices A and B (a distance of INF between
 * them), the path container for that pair will be empty. If A = B, then the
 * the distance will be initially 0 (unless it has a positive loop, then it
 * will initially take the weight of the loop)).
 *
 * This algorithm only works for directed and simple graphs, graphs with
 * parallel edges will give wrong results.
 *
 * If the algorithm finds a negative cycle (including negative loops), it will
 * throw an UnexpectedValueException.
 *
 * If the graph has no vertices, the algorithm will return an empty array, if
 * has no edges, it will return an empty nxn array where n is the number of
 * vertices.
 *
 * @link http://en.wikipedia.org/wiki/Floyd%E2%80%93Warshall_algorithm
 *
 */
class FloydWarshall extends BaseGraph
{

    /**
     * This method updates the cost and path arrays to create new entries if necessary
     * @param $vertexSet array A container with the vertices of the graph.
     * @param $pathCosts array A matrix with calculated costs between vertices.
     * @param $cheapestPaths A matrix where each i, j entry holds an arrsy of Vertices
     * @param $i int index from to update
     * @param $j int index to to update
     * @throws UnexpectedValueException when encountering a negative cycle
     *
     */
    protected function updateInfo(&$vertexSet, &$pathCosts, &$cheapestPaths, $i, $j)
    {

        // If the index pair exists, return
        if (array_key_exists($vertexSet[$i]->getId(), $cheapestPaths) && array_key_exists($vertexSet[$j]->getId(),
            $cheapestPaths[$vertexSet[$i]->getId()])
        ) {

            return;
        }

        // Creating new array indexes if necessary
        if (!array_key_exists($vertexSet[$i]->getId(), $cheapestPaths)) {

            $cheapestPaths[$vertexSet[$i]->getId()] = array();
        }

        if (!array_key_exists($vertexSet[$j]->getId(), $cheapestPaths[$vertexSet[$i]->getId()])) {

            $cheapestPaths[$vertexSet[$i]->getId()][$vertexSet[$j]->getId()] = array();
        }

        if (!array_key_exists($i, $pathCosts)) {

            $pathCosts[$i] = array();
        }

        // If we have an edge between Vertices in positions i, j, we update the cost and assign the edge to the
        // path between them, if not, the cost will be INF
        $edge = $this->getEdgeBetween($vertexSet[$i], $vertexSet[$j]);

        if ($edge) {

            $cheapestPaths[$vertexSet[$i]->getId()][$vertexSet[$j]->getId()] = array($edge);
            $pathCosts[$i][$j] = $edge->getWeight();

        } else {

            if ($i !== $j) {

                $pathCosts[$i][$j] = INF;
            }
        }

    }

    /**
     * Auxiliary method for finding a directed edge between two vertices.
     * @param Vertex $from
     * @param Vertex $to
     * @return \Fhaculty\Graph\Edge\Base|null The edge between $from and $to
     */
    protected function getEdgeBetween(Vertex $from, Vertex $to)
    {

        $edges = $from->getEdgesTo($to);
        return count($edges) > 0 ? $edges[0] : null;
    }

    /**
     * Get all edges on shortest path for every vertex in the graph where this
     * vertex belongs.
     *
     * @return FloydWarshallResult A Result object with the interface for handling the
     * generated edge table list contains the shortest path from i to j
     * @throws UnexpectedValueException when encountering a cycle with
     * negative weight.
     */
    public function createResult()
    {
        $totalCostCheapestPathFromTo = array();
        $cheapestPathFromTo = array();
        $vertexSet = array_values($this->graph->getVertices());

        $nVertices = count($vertexSet);

        //Total cost from i to i is 0
        for ($i = 0; $i < $nVertices; ++$i) {

            $totalCostCheapestPathFromTo[$i][$i] = 0;
        }

        // Calculating shortestPath(i,j,k+1) = min(shortestPath(i,j,k),shortestPath(i,k+1,k) + shortestPath(k+1,j,k))
        for ($k = 0; $k < $nVertices; ++$k) {

            for ($i = 0; $i < $nVertices; ++$i) {

                $this->updateInfo($vertexSet, $totalCostCheapestPathFromTo, $cheapestPathFromTo, $i, $k);

                for ($j = 0; $j < $nVertices; ++$j) {

                    $this->updateInfo($vertexSet, $totalCostCheapestPathFromTo, $cheapestPathFromTo, $i, $k);
                    $this->updateInfo($vertexSet, $totalCostCheapestPathFromTo, $cheapestPathFromTo, $i, $j);
                    $this->updateInfo($vertexSet, $totalCostCheapestPathFromTo, $cheapestPathFromTo, $k, $j);

                    // If we find that the path from (i, k) + (k, j) is shorter than the path (i, j), the new path is
                    // calculated.
                    if ($totalCostCheapestPathFromTo[$i][$k] + $totalCostCheapestPathFromTo[$k][$j] < $totalCostCheapestPathFromTo[$i][$j]) {

                        $totalCostCheapestPathFromTo[$i][$j] = $totalCostCheapestPathFromTo[$i][$k] + $totalCostCheapestPathFromTo[$k][$j];

                        // Testing if we are not repeating a vertex' path over itself before merging the paths
                        if ($vertexSet[$i]->getId() != $vertexSet[$k]->getId() || $vertexSet[$k]->getId() != $vertexSet[$j]->getId()) {

                            $cheapestPathFromTo[$vertexSet[$i]->getId()][$vertexSet[$j]->getId()] = array_merge($cheapestPathFromTo[$vertexSet[$i]->getId()][$vertexSet[$k]->getId()],
                                $cheapestPathFromTo[$vertexSet[$k]->getId()][$vertexSet[$j]->getId()]);
                        }

                    }

                }

            }

            if($totalCostCheapestPathFromTo[$k][$k] < 0) {

                throw new UnexpectedValueException('Floyd-Warshall not supported for negative cycles');
            }
        }

        return new FloydWarshallResult($cheapestPathFromTo, $this->graph);
    }

}