<?php
class AlgorithmSpMooreBellmanFord extends AlgorithmSp{
    
    /**
     *
     *
     * @param array[Edge]   $edges
     * @param array[int]    $totalCostOfCheapestPathTo
     * @param array[Vertex] $predecessorVertexOfCheapestPathTo
     *
     * @return Vertex|NULL
     */
    private function bigStep(&$edges,&$totalCostOfCheapestPathTo,&$predecessorVertexOfCheapestPathTo){
        $changed = NULL;
        foreach ($edges as $edge){                                                //check for all edges
            foreach($edge->getVerticesTarget() as $toVertex){                        //check for all "ends" of this edge (or for all targetes)
                $fromVertex = $edge->getVertexFromTo($toVertex);
                
                if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])){            //If the fromVertex already has a path
                    $newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight(); //New possible costs of this path
                
                    if (! isset($totalCostOfCheapestPathTo[$toVertex->getId()])                //No path has been found yet
                            || $totalCostOfCheapestPathTo[$toVertex->getId()] > $newCost){        //OR this path is cheaper than the old path
                        
                        $changed = $toVertex;
                        $totalCostOfCheapestPathTo[$toVertex->getId()] = $newCost;
                        $predecessorVertexOfCheapestPathTo[$toVertex->getId()] = $fromVertex;
                    }
                }
            }
        }
        return $changed;
    }
    
    /**
     * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
     * 
     * @return array[Edge]
     * @throws NegativeCycleException if there is a negative cycle
     */
    public function getEdges(){
        $totalCostOfCheapestPathTo  = array($this->startVertex->getId() => 0);            //start node distance
        
        $predecessorVertexOfCheapestPathTo  = array($this->startVertex->getId() => $this->startVertex);    //predecessor
        
        $numSteps = $this->startVertex->getGraph()->getNumberOfVertices() - 1; // repeat (n-1) times
        $edges = $this->startVertex->getGraph()->getEdges();
        $changed = true;
        for ($i = 0; $i < $numSteps && $changed; ++$i){                        //repeat n-1 times
            $changed = $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo);
        }
        
        //algorithm is done, build graph
        $returnEdges = $this->getEdgesCheapestPredecesor($predecessorVertexOfCheapestPathTo);
        
        //Check for negative cycles (only if last step didn't already finish anyway)
        if($changed && $changed = $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo)){ // something is still changing...
            $this->throwCycle($changed,$predecessorVertexOfCheapestPathTo);
        }
        
        return $returnEdges;
    }
    
    private function throwCycle(Vertex $vertex,&$predecessorVertexOfCheapestPathTo){
        $vid = $vertex->getId();
        $vertices = array();                                                   // build array of vertices in cycle
        do{
        	$vertices[$vid] = $vertex;                                          // add new vertex to cycle
        
        	$vertex = $predecessorVertexOfCheapestPathTo[$vid];                 // get predecessor of vertex
        	$vid = $vertex->getId();
        }while(!isset($vertices[$vid]));                                      // continue until we find a vertex that's already in the circle (i.e. circle is closed)
        
        $vertices = array_reverse($vertices,true);                             // reverse cycle, because cycle is actually built in opposite direction due to checking predecessors
        
        throw new NegativeCycleException('Negative cycle found',$vertices);
    }
}
