<?php

class AlgorithmMCFCycleCanceling extends AlgorithmMCF {
    
    public function createGraph() {
        
        $resultGraph = $this->graph->createGraphClone();
        //initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten
        
        $edges = $resultGraph->getEdges();                                        //initial flow of edges
        
        
        //todo !
        //         
        return $resultGraph;
    }
    
}