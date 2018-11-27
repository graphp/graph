<?php

namespace Graphp\Graph\Set;

/**
 * Basic interface for every class that provides access to its Set of Vertices
 */
interface VerticesAggregate
{
    /**
     * @return Vertices
     */
    public function getVertices();
}
