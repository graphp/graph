<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use \SplPriorityQueue;
use Fhaculty\Graph\Exception\UnderflowException;

class NearestNeighbor implements Base
{
    public function createResult(Vertex $startVertex)
    {
        return new ResultFromEdges($startVertex, $this->getEdges($startVertex));
    }

    /**
     *
     * @return Edges
     */
    private function getEdges(Vertex $startVertex)
    {
        $returnEdges = array();

        $n = count($startVertex->getGraph()->getVertices());

        $vertex = $startVertex;
        $visitedVertices = array($vertex->getId() => true);

        for ($i = 0; $i < $n - 1; ++$i,
                                    // n-1 steps (spanning tree)
                                    $vertex = $nextVertex) {

            // get all edges from the aktuel vertex
            $edges = $vertex->getEdgesOut();

            $sortedEdges = new SplPriorityQueue();

            // sort the edges
            foreach ($edges as $edge) {
                $sortedEdges->insert($edge, - $edge->getWeight());
            }

            // Untill first is found: get cheepest edge
            foreach ($sortedEdges as $edge) {

                // Get EndVertex of this edge
                $nextVertex = $edge->getVertexToFrom($vertex);

                // is unvisited
                if (!isset($visitedVertices[$nextVertex->getId()])) {
                    break;
                }
            }

            // check if there is a way i can use
            if (isset($visitedVertices[$nextVertex->getId()])) {
                throw new UnderflowException('Graph is not complete - can\'t find an edge to unconnected vertex');
            }

            $visitedVertices[$nextVertex->getId()] = TRUE;

            // clone edge in new Graph
            $returnEdges []= $edge;

        }

        // check if there is a way from end edge to start edge
        // get first connecting edge
        // connect the last vertex with the start vertex
        $returnEdges []= $vertex->getEdgesTo($startVertex)->getEdgeFirst();

        return new Edges($returnEdges);
    }
}
