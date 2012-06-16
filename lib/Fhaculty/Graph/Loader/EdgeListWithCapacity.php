<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Graph;

class EdgeListWithCapacity extends File{
    public function createGraph(){
        
        $graph = new Graph();
        
        $file = $this->getLines();
        
        $graph->createVertices($this->readInt($file[0]));
        
        unset($file[0]);
        foreach ($file as $zeile) {
            $parts = $this->readLine($zeile,array('vertex','vertex','float'),$graph);
            
            if ($this->directedEdges){
                $edge = $parts[0]->createEdgeTo($parts[1]);
            }
            else {
                $edge = $parts[0]->createEdge($parts[1]);
            }
            
            $edge->setCapacity($parts[2]);
        }
        
        return $graph;
        
    }    
}
