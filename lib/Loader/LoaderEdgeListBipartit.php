<?php

class LoaderEdgeListBipartit extends LoaderFile{
    public function createGraph(){

        $graph = new Graph();

        $file = $this->getLines();

        $graph->createVertices($this->readInt($file[0]));

        $countOfAllVertices = $file[0];
        $countOfVerticesInA = $file[1];
        
        for ($i = 0; $i < $countOfVerticesInA; $i++){
            $graph->getVertex($i)->setBalance(11);
        }

        for($k = $countOfVerticesInA; $k < $countOfAllVertices; $k++){
            $graph->getVertex($k)->setBalance(22);
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
