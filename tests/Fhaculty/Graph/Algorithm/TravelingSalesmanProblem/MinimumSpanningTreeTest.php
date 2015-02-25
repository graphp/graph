<?php

use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\MinimumSpanningTree;

class MinimumSpanningTreeTest extends BaseTravelingSalesmanProblemTest
{
    protected function createAlg()
    {
        return new MinimumSpanningTree();
    }
}
