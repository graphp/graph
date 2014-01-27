<?php

namespace Fhaculty\Graph\Exporter;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Renderer\GraphViz;

class Dot implements ExporterInterface
{
    public function getOutput(Graph $graph)
    {
        $graphviz = new GraphViz($graph);
        return $graphviz->createScript();
    }
}
