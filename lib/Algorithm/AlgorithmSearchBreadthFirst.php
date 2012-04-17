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
    
    public function getVerticesIds(){
        return array_keys($this->getVertices());
    }
}
