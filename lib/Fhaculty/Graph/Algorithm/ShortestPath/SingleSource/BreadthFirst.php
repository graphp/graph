<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\Edges;
use \Exception;

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
class BreadthFirst implements Base
{
    public function createResult(Vertex $startVertex)
    {
        return new ResultFromEdgeMap($startVertex, $this->getEdgesMap($startVertex));
    }

    /**
     * get array of edges on the walk for each vertex (vertex ID => array of walk edges)
     *
     * @return array[]
     */
    private function getEdgesMap(Vertex $startVertex)
    {
        $vertexQueue = array();
        $edges = array();

        // $edges[$this->vertex->getId()] = array();

        $vertexCurrent = $startVertex;
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
