<?php

class AlgorithmSearchBreadthFirst{
    public function __construct(Vertex $startVertex){
        $this->vertex = $startVertex;
    }
    
    /**
     * 
     * @return array[Vertex]
     */
    public function getVertices(){
        $queue = array($this->vertex);
        $mark = array($this->vertex->getId() => true);							//to not add vertices twice in array visited
        $visited = array();														//visited vertices
        
        do{
            $t = array_shift($queue);												// get first from queue
            $visited[$t->getId()]= $t;												//save as visited
            
            $vertices = $t->getVerticesEdgeTo();									//get next vertices
            foreach($vertices as $id=>$vertex){
                if(!isset($mark[$id])){													//if not "toughed" before
                    $queue[] = $vertex;														//add to queue
                    $mark[$id] = true;														//and mark
                }
            }
        
        }while($queue);															//untill queue is empty
        
        return $visited;
    }
    
    /**
     * 
     * @param Vertex $destinationVertex
     * @return Graph with lowest vertex count between start and destination
     */
    public function getGraphPathTo($destinationVertex){
    	$queue = array($this->vertex);                                          //Start vertex
    	$mark = array($this->vertex->getId() => true);							//to not add vertices twice in array visited
    	
    	$deepSearchThree = $this->vertex->getGraph()->createGraphCloneEdgeless();   //create copy of graph wihtout edges
    	$visited = array();														//visited vertices
    
    	do{
    		$t = array_shift($queue);											//get first from queue
    		$visited[$t->getId()]= $t;											//save as visited
    
    		$vertices = $t->getVerticesEdgeTo();								//get next vertices
    		foreach($vertices as $id=>$vertex){
    			if(!isset($mark[$id])){											//if not "toughed" before
    				$queue[] = $vertex;											//add to queue
    				$mark[$id] = true;			                                //and mark
    															
    				$deepSearchThree->addEdge($deepSearchThree->getVertex($t->getId())->createEdgeTo($vertex));
    			}
    		}
    		
    		if(isset($mark[$destinationVertex])){                               //stop if destination node is reached
    		    $queue=null;
    		}
    
    	}while($queue);															//untill queue is empty
    	
    	//remove not used edges
    	$destinationVertexInNewGraph = $deepSearchThree->getVertex($destinationVertex->getId());
    	
    	
    	// create path
        $path = new Graph();
        $path->createVertexClone($destinationVertexInNewGraph);                 //add clone of destination vertex to path
        $currentVertex=$destinationVertexInNewGraph;
        
        
        do{                                                                     // run from the buttom to top to the tree and add the predecessor until the startvertex is reached
            $edge=$currentVertex->getIngoingEdges();                            // can only one because of the deepsearch tree
            
            $upperVertex=$edge[0]->getVertexFromTo($currentVertex);
            $path->createVertexClone($upperVertex);                             // clone upper vertex to graph
            $path->createEdgeClone($edge[0]);                                   // clone connecting edge
            $currentVertex=$upperVertex;

        }
        while($currentVertex->getId()!==$this->vertex->getId());
        
       
    
    	return $path;
    }
    
    public function getVerticesIds(){
        return array_keys($this->getVertices());
    }
}
