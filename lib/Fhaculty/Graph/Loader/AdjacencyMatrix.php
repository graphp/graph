<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Graph;

class AdjacencyMatrix extends File
{
    public function createGraph()
    {
        $graph = new Graph();

        $file = $this->getLines();
        $vertexCount = $this->readInt($file[0]);
        $edgeCounter = 0;

        if (count($file) !== ($vertexCount + 1)) {
            throw new UnexpectedValueException('Expects ' . ($vertexCount + 1) . ' lines, but found ' . count($file));
        }

        $graph->createVertices($vertexCount);

        $parts = array_fill(0, $vertexCount, 'int');

        for ($i = 0; $i < $vertexCount; $i++) {

            // Add Vertices
            $this->writeDebugMessage("Adding vertex $i, ");

            $thisVertex = $graph->getVertex($i);

            $currentEdgeList = $this->readLine($file[$i + 1], $parts);
            //$currentEdgeList = explode("\t", $file[$i + 1]);

            for ($k = 0; $k < $vertexCount; $k++) {

                // Add edges
                if ($currentEdgeList[$k] != 0) {

                    $this->writeDebugMessage(" and edge #$edgeCounter: $i -> $k ");

                    if ($this->directedEdges) {
                        $thisVertex->createEdgeTo($graph->getVertex($k));
                    } else {
                        $thisVertex->createEdge($graph->getVertex($k));
                    }

                    $edgeCounter++;
                }

            }
            $this->writeDebugMessage("\n");
        }

        return $graph;
    }
}
