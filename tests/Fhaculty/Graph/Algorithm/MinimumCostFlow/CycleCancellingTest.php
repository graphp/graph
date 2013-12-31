<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Algorithm\MinimumCostFlow\CycleCanceling;

class CycleCancellingTest extends BaseMcfTest
{
    protected function createAlgorithm(Graph $graph)
    {
        return new CycleCanceling($graph);
    }
}
