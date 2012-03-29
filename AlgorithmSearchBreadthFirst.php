<?php

class AlgorithmSearchBreadthFirst{
    public function __construct(Vertex $startVertex){
        $this->vertex = $startVertex;
    }
    
    public function getVertices(){
        $queue = array($this->vertex);
        $mark = array($this->vertex->getId()=>$this->vertex->getId());
        $visited = array();
        
        do{
            $t = array_shift($queue); // get first from queue
            $visited[$t->getId()]= $t;
            
            $vertices = $t->getVerticesEdgeTo();
            foreach($vertices as $id=>$vertex){
                if(!isset($mark[$id])){
                    $queue[] = $vertex;
                    $mark[$id] = $id;
                }
            }
        
        }while($queue);
        
        return $visited;
    }
    
    public function getVerticesIds(){
        return array_keys($this->getVertices());
    }
}
