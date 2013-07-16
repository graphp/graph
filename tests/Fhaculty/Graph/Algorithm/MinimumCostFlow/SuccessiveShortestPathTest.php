<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Algorithm\MinimumCostFlow\SuccessiveShortestPath;

class SuccessiveShortestPathTest extends BaseMcfTest
{
    protected function createAlgorithm(Graph $graph)
    {
        return new SuccessiveShortestPath($graph);
    }
}
