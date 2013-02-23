<?php

namespace Fhaculty\Graph\Exporter;

use Fhaculty\Graph\Edge\Directed;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;

/**
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Trivial_Graph_Format
 */
class TrivialGraphFormat implements ExporterInterface
{
    const EOL = PHP_EOL;

    public function getOutput(Graph $graph)
    {
        $output = '';

        // build an array to map vertex IDs (which may contain complex strings) to temporary numeric IDs for output
        $tid = 1;
        $tids = array();

        foreach ($graph->getVertices() as $vid => $vertex) {
            $output .= $tid . ' ' . $this->getVertexLabel($vertex) . self::EOL;
            $tids[$vid] = $tid++;
        }

        // end of vertex list, start of edge list
        $output .= '#' . self::EOL;

        foreach ($graph->getEdges() as $edge) {
            $ids = $edge->getVerticesId();
            $a = $tids[$ids[0]];
            $b = $tids[$ids[1]];

            $label = $this->getEdgeLabel($edge);
            if ($label !== '') {
                $label = ' ' . $label;
            }

            $output .= $a . ' ' . $b . $label . self::EOL;

            // this is not a directed edge => also add back-edge with same label
            if (!($edge instanceof Directed)) {
                $output .= $b . ' ' . $a . $label . self::EOL;
            }
        }
        return $output;
    }

    protected function getVertexLabel(Vertex $vertex)
    {
        // TODO: dump additional vertex attributes, such as group, balance, etc.
        return $vertex->getId();
    }

    protected function getEdgeLabel(Edge $edge)
    {
        // TODO: dump additional edge attributes, such as flow, capacity, weight, etc.
        return '';
    }
}
