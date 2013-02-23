<?php

namespace Fhaculty\Graph\Exporter;

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;

class Dot implements ExporterInterface
{
    public function getOutput(Graph $graph)
    {
        $graphviz = new GraphViz($graph);
        return $graphviz->createScript();
    }
}
