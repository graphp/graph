<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Graph;

class EdgeListWithWeightedCapacityAndBalance extends File
{
    /**
     *
     */
    public function createGraph()
    {
        $graph = new Graph();

        $file = $this->getLines();

        $graph->createVertices($this->readInt($file[0]));
        unset($file[0]);

        // set the value of the vertices
        $zeile = 1;
        foreach ($graph->getVertices() as $vertex) {
            $vertex->setBalance($this->readFloat($file[$zeile]));
            unset($file[$zeile]);
            ++$zeile;
        }

        foreach ($file as $zeile) {
            $parts = $this->readLine($zeile, array('vertex', 'vertex', 'float', 'float'), $graph);

            if ($this->directedEdges) {
                $edge = $parts[0]->createEdgeTo($parts[1]);
            } else {
                $edge = $parts[0]->createEdge($parts[1]);
            }

            $edge->setWeight($parts[2]);
            $edge->setCapacity($parts[3]);
        }

        return $graph;

    }
}
