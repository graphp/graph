<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Exception\UnexpectedValueException;
use \SplPriorityQueue;

class Dijkstra extends Base
{
    /**
     * get all edges on shortest path for this vertex
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        $totalCostOfCheapestPathTo  = Array();
        $totalCostOfCheapestPathTo[$this->startVertex->getId()] = 0;            //start node distance

        $cheapestVertex = new SplPriorityQueue();                                //just to get the cheapest vertex in the correct order
        $cheapestVertex->insert($this->startVertex, 0);

        $predecesVertexOfCheapestPathTo  = Array();                                //predecessor
        $predecesVertexOfCheapestPathTo[$this->startVertex->getId()] = $this->startVertex;

        $usedVertices  = Array();                                               //mark vertices when their cheapest path has been found

        // Repeat until all vertices have been marked
        $totalCountOfVertices = $this->startVertex->getGraph()->getNumberOfVertices();
        for ($i = 0; $i < $totalCountOfVertices; ++$i) {
            $currentVertex = NULL;
            $currentVertexId = NULL;
            $isEmpty = false;
            do {
                if ($cheapestVertex->isEmpty()) {                                //if the priority queue is empty there are isolated vertices, but the algorithm visited all other vertices
                    $isEmpty = true;
                    break;
                }
                $currentVertex = $cheapestVertex->extract();                    //Get cheapest unmarked vertex
                $currentVertexId = $currentVertex->getId();
            } while ( isset($usedVertices[$currentVertexId]) );                    //Vertices can be in the priority queue multiple times, with different path costs (if vertex is already marked, this is an old unvalid entry)

            if ($isEmpty) {                                                        //catch "algorithm ends" condition
                break;
            }

            $usedVertices[$currentVertexId] = true;                                //mark this vertex

            foreach ($currentVertex->getEdgesOut() as $edge) {                 //check for all edges of current vertex if there is a cheaper path (or IN OTHER WORDS: Add reachable nodes from currently added node and refresh the current possible distances)
                $weight = $edge->getWeight();
                if ($weight < 0) {
                    throw new UnexpectedValueException('Djkstra not supported for negative weights - Consider using MooreBellmanFord');
                }

                $targetVertex = $edge->getVertexToFrom($currentVertex);
                $targetVertexId = $targetVertex->getId();

                if ( ! isset( $usedVertices[$targetVertexId] ) ) {              //if the targetVertex is marked, the cheapest path for this vertex has already been found (no negative edges) {
                    $newCostsToTargetVertex = $totalCostOfCheapestPathTo[$currentVertexId] + $weight;    //calculate new cost to vertex

                    if ( ( ! isset($predecesVertexOfCheapestPathTo[$targetVertexId]) )
                           || $totalCostOfCheapestPathTo[$targetVertexId] > $newCostsToTargetVertex){    //is the new path cheaper?

                        $cheapestVertex->insert($targetVertex, - $newCostsToTargetVertex);            //Not an update, just an new insert with lower cost
                                                                                                    // so the lowest cost will be extraced first
                                                                                                    // and higher cost will be skipped during extraction

                        $totalCostOfCheapestPathTo[$targetVertexId] = $newCostsToTargetVertex;    // update/set costs found with the new connection
                        $predecesVertexOfCheapestPathTo[$targetVertexId] = $currentVertex;        // update/set predecessor vertex from the new connection
                    }
                }
            }
        }

        //algorithm is done, return resulting edges
        return $this->getEdgesCheapestPredecesor($predecesVertexOfCheapestPathTo);
    }
}
