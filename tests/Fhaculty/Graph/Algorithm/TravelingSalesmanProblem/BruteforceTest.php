<?php

use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\Bruteforce;

class BruteforceTest extends BaseTravelingSalesmanProblemTest
{
    protected function createAlg()
    {
        return new Bruteforce();
    }
}
