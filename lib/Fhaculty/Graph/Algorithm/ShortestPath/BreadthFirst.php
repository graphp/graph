<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Exception\InvalidArgumentException;

use Fhaculty\Graph\Exception\OutOfBoundsException;

use Fhaculty\Graph\Walk;

use Fhaculty\Graph\Vertex;
use \Exception;

class BreadthFirst extends Base
{
    /**
     * get distance between start vertex and given end vertex
     *
     * @param  Vertex               $endVertex
     * @throws OutOfBoundsException if there's no path to given end vertex
     * @return int
     * @uses self::getEdgesTo()
     */
    public function getDistance(Vertex $endVertex)
    {
        return count($this->getEdgesTo($endVertex));
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

    public function getEdgesTo(Vertex $endVertex)
    {
        if ($endVertex->getGraph() === $this->vertex->getGraph()) {
            $map = $this->getEdgesMap();

            if (isset($map[$endVertex->getId()])) {
                return $map[$endVertex->getId()];
            }
        }
        throw new OutOfBoundsException('Given target vertex can not be reached from start vertex');
    }

    /**
     * get map of vertex IDs to distance
     *
     * @return int[]
     * @uses Vertex::hasLoop()
     */
    public function getDistanceMap()
    {
        $ret = array();
        foreach ($this->getEdgesMap() as $vid => $edges) {
            $ret[$vid] = count($edges);
        }

        return $ret;
    }

    /**
     * get array of all target vertices this vertex has a path to
     *
     * @return Vertex[]
     * @uses self::getEdgesMap()
     */
    public function getVertices()
    {
        $ret = array();
        $graph = $this->vertex->getGraph();
        foreach ($this->getEdgesMap() as $vid => $unusedEdges) {
            $ret[$vid] = $graph->getVertex($vid);
        }

        return $ret;
    }

    public function getEdges()
    {
        $ret = array();
        foreach ($this->getEdgesMap() as $edges) {
            foreach ($edges as $edge) {
                if (!in_array($edge, $ret, true)) {
                    $ret []= $edge;
                }
            }
        }

        return $ret;
    }
}
