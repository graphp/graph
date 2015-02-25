<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;

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
     * @throws UnderflowException if the given graph is not complete
     */
    public function createResult(Vertex $startVertex);
}
