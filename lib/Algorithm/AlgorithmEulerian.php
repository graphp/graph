<?php

class AlgorithmEulerian extends Algorithm{
    public function __construct($graph){
        $this->graph = $graph;
    }
    
    /**
     * check whether this graph has an eulerian cycle
     *
     * @return boolean
     * @uses Graph::isConsecutive()
     * @uses Vertex::getDegree()
     * @todo isolated vertices should be ignored
     * @todo definition is only valid for undirected graphs
     */
    public function hasCycle(){
        if($this->graph->isConsecutive()){
            foreach($this->graph->getVertices() as $vertex){
                if($vertex->getDegree() & 1){ // uneven degree => fail
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
