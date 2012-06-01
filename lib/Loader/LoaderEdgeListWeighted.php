<?php

class LoaderEdgeListWeighted extends LoaderFile{
    public function createGraph(){
        
        $graph = new Graph();
        
        $file = $this->getLines();
        $vertexCount = $file[0];
        $edgeCounter = 0;
        
        $graph->createVertices($vertexCount);
        
        unset($file[0]);
        foreach ($file as $zeile) {
            $edgeConnections = explode("\t", $zeile);
            
            $from = $graph->getVertex($edgeConnections[0]);
            $to = $graph->getVertex($edgeConnections[1]);
            
            $edge;
            
            if ($this->directedEdges){
                $edge = $from->createEdgeTo($to);
            }
            else {
                $edge = $from->createEdge($to);
            }
            
            $edge->setWeight((float)$edgeConnections[2]);
        }
        
        return $graph;
        
    }    
}
