<?php

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Prim;

class PrimTest extends BaseMstTest
{
    protected function createAlg(Vertex $vertex)
    {
        return new Prim($vertex);
    }
}
