<?php

use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\NearestNeighbor;

class NearestNeighborTest extends BaseTravelingSalesmanProblemTest
{
    protected function createAlg()
    {
        return new NearestNeighbor();
    }
}
