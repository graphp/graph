<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;

/**
 * Class FloydWarshall
 *
 * An implementation of the Floyd Warshall algorithm to find the shortest path from each vertex to each vertex.
 * @link http://en.wikipedia.org/wiki/Floyd%E2%80%93Warshall_algorithm
 *
 * @package Fhaculty\Graph\Algorithm\ShortestPath
 */
class FloydWarshall
{

    /**
     * @var Vertex
     */
    protected $vertex;

    public function __construct($s)
    {

        if (!(get_class($s) === 'Fhaculty\Graph\Vertex')) {

            throw new InvalidArgumentException('This algorithm only receives Vertex objects.');
        }

        $this->vertex = $s;
    }

    /**
     * This method updates the cost and path arrays to create new entries if necessary
     * @param $vertexSet array A container with the vertices of the graph.
     * @param $pathCosts array A matrix with calculated costs between vertices.
     * @param $cheapestPaths A matrix where each i, j entry holds an arrsy of Vertices
     * @param $i int index from to update
     * @param $j int index to to update
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

        // If we have an edge between Vertices in positions i, j, we update the cost and asign the edge to the
        // path between them, if not, the cost will be INF
        $edge = $this->getEdgeFromTo($vertexSet[$i], $vertexSet[$j]);

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
     * Auxiliary method for findind a directed edge between two vertices.
     * @param Vertex $from
     * @param Vertex $to
     * @return \Fhaculty\Graph\Edge\Base|null The edge between $from and $to
     */
    protected function getEdgeFromTo(Vertex $from, Vertex $to)
    {
        foreach ($from->getEdges() as $edge) {
            if ($edge->isConnection($from, $to)) {
                return $edge;
            }
        }

        return null;
    }

    /**
     * Get all edges on shortest path for every vertex in the graph where this vertex belongs.
     *
     * @return Edge[][][] For each i, j pair position of this array, an Edge list contains the shortest path from i to j
     */
    public function getEdges()
    {
        $totalCostCheapestPathFromTo = array();
        $cheapestPathFromTo = array();
        $vertexSet = array_values($this->vertex->getGraph()->getVertices());

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
        }

        return $cheapestPathFromTo;
    }

}