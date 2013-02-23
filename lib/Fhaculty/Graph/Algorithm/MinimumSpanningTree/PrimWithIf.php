<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Vertex;
use \SplPriorityQueue;
use \Exception;

class PrimWithIf extends Base
{
    /**
     * @var Vertex
     */
    private $startVertex;

    public function __construct(Vertex $startVertex)
    {
        $this->startVertex = $startVertex;
    }

    /**
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        // Initialize algorithm
        $edgeQueue = new SplPriorityQueue();
        $vertexCurrent = $this->startVertex;

        $markInserted = array();
        $returnEdges = array();

        // iterate n-1 times (per definition, resulting MST MUST have n-1 edges)
        for ($i = 0, $n = $this->startVertex->getGraph()->getNumberOfVertices() - 1; $i < $n; ++$i) {
            $markInserted[$vertexCurrent->getId()] = true;

            // get unvisited vertex of the edge and add edges from new vertex
            // Add all edges from $currentVertex to priority queue
            foreach ($vertexCurrent->getEdges() as $currentEdge) {

                // TODO maybe it would be better to check if the reachable vertex of $currentEdge si allready marked (smaller Queue vs. more if's)
                if (!isset($markInserted[$currentEdge->getVertexToFrom($vertexCurrent)->getId()])) {
                    // Add edges to priority queue with inverted weights (priority queue has high values at the front)
                    $edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
                }
            }

            do {
                try {
                    // Get next cheapest edge
                    $cheapestEdge = $edgeQueue->extract();
                } catch (Exception $e) {
                    return $returnEdges;
                    throw new UnexpectedValueException('Graph has more as one component');
                }

                // Check if edge is between unmarked and marked edge

                $startVertices = $cheapestEdge->getVerticesStart();
                $vertexA = $startVertices[0];
                $vertexB = $cheapestEdge->getVertexToFrom($vertexA);

            // Edge is between marked and unmared vertex
            } while (!(isset($markInserted[$vertexA->getId()]) XOR isset($markInserted[$vertexB->getId()])));

            // Cheapest Edge found, add edge to returnGraph
            $returnEdges []= $cheapestEdge;

            // set current vertex for next iteration in order to add its edges to queue
            if (isset($markInserted[$vertexA->getId()])) {
                $vertexCurrent = $vertexB;
            } else {
                $vertexCurrent = $vertexA;
            }
        }

        return $returnEdges;
    }

    protected function getGraph()
    {
        return $this->startVertex->getGraph();
    }
}
