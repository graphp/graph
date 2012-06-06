<?php
class AlgorithmMstPrim extends AlgorithmMst{
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
        
        $startVertexId = $this->startVertex->getId();                            // set start vertex id
        
        // Initialize algorithm 
        
        $markInserted = array($startVertexId => true);                // Color starting vertex
                    
        foreach ($this->startVertex->getEdges() as $currentEdge) {                // Add all edges from startvertex
            $edgeQueue->insert($currentEdge, -$currentEdge->getWeight());        // Add edges to priority queue with inverted weights (priority queue has high values at the front)
        }
        // END Initialize algorithm


        // BEGIN algorithm
        
        $returnEdges = array();
        
        $vertices = $this->startVertex->getGraph()->getVertices();
        unset($vertices[$startVertexId]);                                      // skip the first entry to run only n-1 times 
        
         foreach ($vertices as $value) {                                            // iterate n times over edges form know nodes
            $cheapestEdge = $edgeQueue->extract();                                // Get next cheapest edge

            // BEGIN Check if edge is is: [visiteted]->[unvisited]
            $cheapestEdgeIsOk = false;                                            // 
            while($cheapestEdgeIsOk == false) {
                foreach ($cheapestEdge->getVerticesTarget() as $currentTarget){    // run over both vertices

                    $cheapestEdgeIsOkOld = $cheapestEdgeIsOk;                    

                    $cheapestEdgeIsOk = $cheapestEdgeIsOk ? true : !isset($markInserted[$currentTarget->getId()]); //check if already visited, if not visit

                    if($cheapestEdgeIsOkOld != $cheapestEdgeIsOk){                //get unvisted vertex                
                        $newTargetVertex = $currentTarget;
                    }
                }
                if($cheapestEdgeIsOk == false){                                    // check if cheapest edge is false
                    $cheapestEdge = $edgeQueue->extract();                        //if edge is not ok, get a new edge from the queue
                }
            }
            // END Check if edge is is: [visiteted]->[unvisited]

            
            // BEGIN Cheapest Edge found, add new vertex and edge to returnGraph
            
            $markInserted[$newTargetVertex->getId()] = true;
                
            $returnEdges []= $cheapestEdge;

            // BEGIN get unvisited vertex of the edge and add edges from new vertex
            if($newTargetVertex->getId() != $startVertexId){
                        
                foreach ($newTargetVertex->getEdges() as $currentEdge) {        // Add all edges from $currentVertex to priority queue
                    $edgeQueue->insert($currentEdge, -$currentEdge->getWeight());
                }
            }
            // END get unvisited vertex of the edge and add edges from new vertex
        }
        // END algorithm
        return $returnEdges;
    }
}
