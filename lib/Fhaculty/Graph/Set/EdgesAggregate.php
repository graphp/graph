<?php

namespace Fhaculty\Graph\Set;

use Fhaculty\Graph\Set\Edges;

interface EdgesAggregate
{
    /**
     * @return Edges
     */
    public function getEdges();
}
