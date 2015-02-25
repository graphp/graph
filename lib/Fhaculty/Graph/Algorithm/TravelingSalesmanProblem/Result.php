<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\EdgesAggregate;

interface Result extends EdgesAggregate
{
    /**
     * get resulting graph with the (first) best circle of edges connecting all vertices
     *
     * @throws Exception on error
     * @return Graph
     */
    public function createGraph();

    /**
     * get (first) best circle connecting all vertices
     *
     * @return Walk
     */
    public function getCycle();

    public function getWeight();

    /**
     * get array of edges connecting all vertices in a circle
     *
     * @return Edges
     */
    // public function getEdges();
}
