<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\Edges;
use \Exception;
use Fhaculty\Graph\Exception\BadMethodCallException;

/**
 * Simple breadth-first shortest path algorithm
 *
 * This algorithm ignores edge weights and operates as a level-order algorithm
 * on the number of hops. As such, it considers the path with the least number
 * of hops to be shortest.
 *
 * This is particularly useful your Graph doesn't have Edge weights assigned to
 * begin with or if you're merely interested in knowing which Vertices can be
 * reached at all (path finding). This avoids running expensive operations to
 * determine the actual weight (distance) of a path.
 */
class BreadthFirst extends Base
{
    public function createResult()
    {
        $alg = new SingleSource\BreadthFirst();

        return $alg->createResult($this->vertex);
    }
}
