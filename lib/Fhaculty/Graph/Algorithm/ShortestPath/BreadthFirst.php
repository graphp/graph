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
        return new EdgesMapResult($this->vertex, $this->getEdgesMap());
    }

    /**
     * get array of edges on the walk for each vertex (vertex ID => array of walk edges)
     *
     * @return array[]
     */
    public function getEdgesMap()
    {
        $vertexQueue = array();
        $edges = array();

        // $edges[$this->vertex->getId()] = array();

        $vertexCurrent = $this->vertex;
        $edgesCurrent = array();

        do {
            foreach ($vertexCurrent->getEdgesOut() as $edge) {
                $vertexTarget = $edge->getVertexToFrom($vertexCurrent);
                $vid = $vertexTarget->getId();
                if (!isset($edges[$vid])) {
                    $vertexQueue []= $vertexTarget;
                    $edges[$vid] = array_merge($edgesCurrent, array($edge));
                }
            }

            // get next from queue
            $vertexCurrent = array_shift($vertexQueue);
            if ($vertexCurrent) {
                $edgesCurrent = $edges[$vertexCurrent->getId()];
            }
        // untill queue is empty
        } while ($vertexCurrent);

        return $edges;
    }
}
