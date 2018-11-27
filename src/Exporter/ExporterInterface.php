<?php

namespace Graphp\Graph\Exporter;

use Graphp\Graph\Graph;

interface ExporterInterface
{
    public function getOutput(Graph $graph);
}
