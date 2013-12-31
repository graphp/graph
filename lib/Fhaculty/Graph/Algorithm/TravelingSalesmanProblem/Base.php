<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Vertex;

interface Base
{
    /**
     * get resulting (first) best circle of edges connecting all vertices
     *
     * actual start doesn't really matter as we're only considering complete
     * graphs here.
     *
     * @param Vertex $startVertex
     * @return Result
     */
    public function createResult(Vertex $startVertex);
}
