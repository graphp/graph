<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Algorithm\Groups as AlgorithmGroups;
use \Exception;

class EdgeListBipartit extends File{
    public function createGraph(){

        $graph = new Graph();

        $file = $this->getLines();
        
        $countOfAllVertices = $this->readInt($file[0]);
        $countOfVerticesInA = $this->readInt($file[1]);
        
        if($countOfVerticesInA > $countOfAllVertices || $countOfVerticesInA < 0){
            throw new UnexpectedValueException('Invalid value for number of vertices in group 0');
        }

        $graph->createVertices($countOfAllVertices);
        
        for ($i = 0; $i < $countOfVerticesInA; ++$i){
            $graph->getVertex($i)->setGroup(0);
        }

        for($k = $countOfVerticesInA; $k < $countOfAllVertices; ++$k){
            $graph->getVertex($k)->setGroup(1);
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
        
        $alg = new AlgorithmGroups($graph);
        if(!$alg->isBipartit()){
            throw new UnexpectedValueException('Graph read from file does not form a valid bipartit graph');
        }
        
        return $graph;

    }
}
