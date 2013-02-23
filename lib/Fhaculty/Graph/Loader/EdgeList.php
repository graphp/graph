<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Graph;

class EdgeList extends File
{
    public function createGraph()
    {
        $graph = new Graph();

        $file = $this->getLines();
        $vertexCount = $this->readInt($file[0]);

        $this->writeDebugMessage('create '.$vertexCount.' vertices');

        $graph->createVertices($vertexCount);

        $this->writeDebugMessage('parse edges');

        unset($file[0]);
        foreach ($file as $zeile) {
            $ends = $this->readLine($zeile,array('from'=>'vertex','to'=>'vertex'),$graph);

            if ($this->directedEdges) {
                $ends['from']->createEdgeTo($ends['to']);
            } else {
                $ends['from']->createEdge($ends['to']);
            }
        }

        return $graph;

    }
}
