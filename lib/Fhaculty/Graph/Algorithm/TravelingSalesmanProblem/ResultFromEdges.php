<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Edges;

class ResultFromEdges implements Result
{
    private $startVertex;
    private $edges;

    public function __construct(Vertex $startVertex, Edges $edges)
    {
        $this->startVertex = $startVertex;
        $this->edges = $edges;
    }

    public function createGraph()
    {
        return $this->startVertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    public function getCycle()
    {
        return Walk::factoryCycleFromEdges($this->getEdges(), $this->startVertex);
    }

    public function getWeight()
    {
        $weight = 0;
        foreach ($this->getEdges() as $edge) {
            $weight += $edge->getWeight();
        }

        return $weight;
    }

    public function getEdges()
    {
        return $this->edges;
    }
}
