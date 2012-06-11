<?php

class AlgorithmBipartit extends Algorithm{
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }
    
    public function isBipartit(){
        $colors = array();
        
        foreach($this->graph->getVertices() as $vid=>$vertex){
            if(!isset($colors[$vid])){
                $colors[$vid] = 0;
                try{
                    $this->checkVertex($vertex,0, $colors);
                }
                catch(Exception $e){
                    return false;
                }
            }
        }
        return true;
    }
    
    private function checkVertex(Vertex $vertex,$color,&$colors){
        $nextColor = 1-$color;
        foreach($vertex->getVerticesEdge() as $vid=>$nextVertex){
            if(!isset($colors[$vid])){
                $colors[$vid] = $nextColor;
                $this->checkVertex($nextVertex,$nextColor, $colors);
            }else if($colors[$vid] !== $nextColor){
                throw new Exception();
            }
        }
    }
}