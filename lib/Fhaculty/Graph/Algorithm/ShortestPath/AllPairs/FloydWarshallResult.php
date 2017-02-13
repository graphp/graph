<?php


namespace Fhaculty\Graph\Algorithm\ShortestPath\AllPairs;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Walk;

/**
 * Class FloydWarshallResult
 *
 * An interface for handling the results for a Floyd Warshall all pairs
 * shortest path algorithm. The algorithm produces a result for a given graph,
 * and this API helps to get the information about this solution in a simpler
 * way.
 *
 * @package Fhaculty\Graph\Algorithm\ShortestPath\AllPairs
 */
class FloydWarshallResult
{

    /**
     * @var Edge[][][]
     */
    protected $edgeTable;

    /**
     * @var Graph
     */
    protected $graph;

    public function __construct($edgeTable, $graph)
    {

        $this->edgeTable = $edgeTable;
        $this->graph = $graph;
    }

    /**
     * Get a list of edges common to every pair's shortest path.
     *
     * @return Edge[]
     */
    public function getEdges()
    {

        $foundEdges = array();

        foreach ($this->edgeTable as $idx => $vertexRow) {

            foreach ($vertexRow as $idx2 => $vertexCol) {

                foreach ($vertexCol as $edge) {

                    if (!in_array($edge, $foundEdges, true)) {

                       $foundEdges[] = $edge;
                    }
                }
            }
        }

        return $foundEdges;
    }

    /**
     * Creates a new Graph based on the provided graph, without the edges not
     * present in any shortest path.
     *
     * @return Graph The new graph with the edges in each pair's shortest path.
     */
    public function createGraph()
    {

        return $this->graph->createGraphCloneEdges($this->getEdges());
    }

    /**
     * Returns the original Graph provided to this result.
     *
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * Returns an array of paths (Walks), each key of this array is the id of a
     * vertex in the Graph and it references the path (Walk) with the Vertex
     * associated to that Id as the start Vertex for that path.
    +
     * @return Walk[]
     */
    public function getPaths()
    {
        $paths = array();

        foreach ($this->edgeTable as $idx => $vertexRow) {

            foreach ($vertexRow as $idx2 => $vertexCol) {

                $paths[$idx] = Walk::factoryFromEdges($vertexCol, $this->graph->getVertex($idx));
            }
        }

        return $paths;
    }

    /**
     * Returns the original Edge table provided for this result.
     *
     * @return Edge[][][]
     */
    public function getEdgeTable()
    {
        return $this->edgeTable;
    }

}