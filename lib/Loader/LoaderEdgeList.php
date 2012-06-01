<?php

class LoaderEdgeList extends LoaderFile{
    public function createGraph(){
        
        $graph = new Graph();
        
        $file = $this->getLines();
        $vertexCount = $file[0];
        $edgeCounter = 0;
        
        $this->writeDebugMessage('create '.$vertexCount.' vertices');
        
        $graph->createVertices($vertexCount);
        
        $this->writeDebugMessage('parse edges');
        
        unset($file[0]);
        foreach ($file as $zeile) {
            $edgeConnections = explode("\t", $zeile);
            
            $from = $graph->getVertex($edgeConnections[0]);
            $to = $graph->getVertex($edgeConnections[1]);
            
            $from->createEdge($to);                                //TODO directed
        }
        
        return $graph;
        
    }    
}
