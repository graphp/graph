<?php

class LoaderEdgeListBipartit extends LoaderFile{
    public function createGraph(){

        $graph = new Graph();

        $file = $this->getLines();
        
        $countOfAllVertices = $this->readInt($file[0]);
        $countOfVerticesInA = $this->readInt($file[1]);
        
        if($countOfVerticesInA > $countOfAllVertices || $countOfVerticesInA < 0){
            throw new Exception('Invalid value for number of vertices in group A');
        }

        $graph->createVertices($countOfAllVertices);
        
        for ($i = 0; $i < $countOfVerticesInA; ++$i){
            $graph->getVertex($i)->setLayout('label','A');
        }

        for($k = $countOfVerticesInA; $k < $countOfAllVertices; ++$k){
            $graph->getVertex($k)->setLayout('label','B');
        }

        unset($file[0]);
        unset($file[1]);
        foreach ($file as $zeile) {
            $parts = $this->readLine($zeile,array('vertex','vertex'),$graph);

            if ($this->directedEdges){
                $edge = $parts[0]->createEdgeTo($parts[1]);
            }
            else {
                $edge = $parts[0]->createEdge($parts[1]);
            }
        }

        return $graph;

    }
}
