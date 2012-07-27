<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\EdgeUndirectedId;

use Fhaculty\Graph\Vertex;
use \SplPriorityQueue;

class Prim extends Base{
    /**
     * @var Vertex
     */
    private $startVertex;
    
    public function __construct(Vertex $startVertex){
        $this->startVertex = $startVertex;
    }
    
    /**
     *
     * @return array[Edge]
     */
    public function getEdges(){
        $edgeQueue = new SplPriorityQueue();
        
        $startVertexId = $this->startVertex->getId();                           // set start vertex id
        
        // Initialize algorithm 
        
        $markInserted = array($startVertexId => true);                          // Color starting vertex
                    
        foreach ($this->startVertex->getEdges() as $currentEdge) {              // Add all edges from startvertex
            $edgeQueue->insert($currentEdge, -$currentEdge->getWeight());       // Add edges to priority queue with inverted weights (priority queue has high values at the front)
        }
        
        $returnEdges = array();
        // END Initialize algorithm


        // BEGIN algorithm
        
        $vertices = $this->startVertex->getGraph()->getVertices();
        unset($vertices[$startVertexId]);                                       // skip the first entry to run only n-1 times 
        
        foreach ($vertices as $notUsed) {                                       // iterate n-1 times over edges form know nodes
            
            do {
                try {
                    $cheapestEdge = $edgeQueue->extract();                      // Get next cheapest edge
                }
                catch (Exception $e) {
                    throw new Exception("Graph has more as one component");
                }
                
                //Check if edge is between unmarked and marked edge
                
                $startVertices = $cheapestEdge->getVerticesStart();
                $vertexA = $startVertices[0];
                $vertexB = $cheapestEdge->getVertexToFrom($vertexA);
                
            } while ( $markInserted[$vertexA] XOR $markInserted[$vertexB]);     //Edge is between marked and unmared vertex
            
            // BEGIN Cheapest Edge found, add new vertex and edge to returnGraph
            
            if ($markInserted[$vertexA]) {
                $endVertex = $vertexB;
            }
            else {
                $endVertex = $vertexA;
            }
            
            $markInserted[$endVertex->getId()] = true;
                
            $returnEdges []= $cheapestEdge;

            // BEGIN get unvisited vertex of the edge and add edges from new vertex
                        
            foreach ($endVertex->getEdges() as $currentEdge) {                  // Add all edges from $currentVertex to priority queue
                //TODO maybe it would be better to check if the reachable vertex of $currentEdge si allready marked (smaller Queue vs. more if's)
                $edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
            }
            // END get unvisited vertex of the edge and add edges from new vertex
        }
        // END algorithm
        return $returnEdges;
    }
}
