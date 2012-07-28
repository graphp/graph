<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\EdgeDirected;

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
        // Initialize algorithm 
        $edgeQueue = new SplPriorityQueue();
        $vertexCurrent = $this->startVertex;
        
        $markInserted = array();
        $returnEdges = array();

        for($i=0,$n=$this->startVertex->getGraph()->getNumberOfVertices()-1;$i<$n;++$i){ // iterate n-1 times (per definition, resulting MST MUST have n-1 edges)
            $markInserted[$vertexCurrent->getId()] = true;
            
            // get unvisited vertex of the edge and add edges from new vertex
            foreach ($vertexCurrent->getEdges() as $currentEdge) {            // Add all edges from $currentVertex to priority queue
            	if(!$currentEdge->isLoop()){
                    if($currentEdge instanceof EdgeDirected){
                	    throw new UnexpectedValueException('Unable to create MST for directed graphs');
                	}
                    //TODO maybe it would be better to check if the reachable vertex of $currentEdge si allready marked (smaller Queue vs. more if's)
                	$edgeQueue->insert($currentEdge, -$currentEdge->getWeight());   // Add edges to priority queue with inverted weights (priority queue has high values at the front)
            	}
            }
            
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
                
            } while ( isset($markInserted[$vertexA->getId()]) XOR isset($markInserted[$vertexB->getId()]));     //Edge is between marked and unmared vertex
            
            // Cheapest Edge found, add edge to returnGraph
            $returnEdges []= $cheapestEdge;
            
            // set current vertex for next iteration in order to add its edges to queue
            if ($markInserted[$vertexA]) {
                $vertexCurrent = $vertexB;
            }
            else {
                $vertexCurrent = $vertexA;
            }
        }
        // END algorithm
        return $returnEdges;
    }
}
