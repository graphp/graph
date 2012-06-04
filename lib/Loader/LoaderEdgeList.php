<?php

class LoaderEdgeList extends LoaderFile{
    public function createGraph(){
        
        $graph = new Graph();
        
        $file = $this->getLines();
        $vertexCount = $this->readInt($file[0]);
        
        $this->writeDebugMessage('create '.$vertexCount.' vertices');
        
        $graph->createVertices($vertexCount);
        
        $this->writeDebugMessage('parse edges');
        
        unset($file[0]);
        foreach ($file as $zeile) {
            $ends = $this->readLine($zeile,array('from'=>'vertex','to'=>'vertex'),$graph);
            
            $ends['from']->createEdge($ends['to']);    //TODO directed
        }
        
        return $graph;
        
    }    
}
