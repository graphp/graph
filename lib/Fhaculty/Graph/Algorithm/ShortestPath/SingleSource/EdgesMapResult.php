<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\Result;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Set\VerticesMap;

class EdgesMapResult implements Result
{
    private $vertex;
    private $edgeMap;

    public function __construct(Vertex $vertex, array $edgeMap)
    {
        $this->vertex = $vertex;
        $this->edgeMap = $edgeMap;
    }

    /**
     * get distance between start vertex and given end vertex
     *
     * @param  Vertex               $endVertex
     * @throws OutOfBoundsException if there's no path to given end vertex
     * @return int
     * @uses self::getEdgesTo()
     */
    public function getDistanceTo(Vertex $endVertex)
    {
        return count($this->getEdgesTo($endVertex));
    }

    public function getWalkTo(Vertex $endVertex)
    {
        return Walk::factoryFromEdges($this->getEdgesTo($endVertex), $this->vertex);
    }

    public function hasWalkTo(Vertex $vertex)
    {
        return (isset($this->edgeMap[$vertex->getId()]) && $this->vertex->getGraph() === $vertex->getGraph());
    }

    public function createGraph()
    {
        return $this->vertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    public function getEdgesTo(Vertex $endVertex)
    {
        if ($endVertex->getGraph() === $this->vertex->getGraph()) {
            if (isset($this->edgeMap[$endVertex->getId()])) {
                return new Edges($this->edgeMap[$endVertex->getId()]);
            }
        }
        throw new OutOfBoundsException('Given target vertex can not be reached from start vertex');
    }

    /**
     * get map of vertex IDs to distance
     *
     * @return int[]
     */
    public function getDistanceMap()
    {
        $ret = array();
        foreach ($this->edgeMap as $vid => $edges) {
            $ret[$vid] = count($edges);
        }

        return $ret;
    }

    public function getVertices()
    {
        $ret = array();
        $graph = $this->vertex->getGraph();

        foreach ($this->edgeMap as $vid => $unusedEdges) {
            $ret[$vid] = $graph->getVertex($vid);
        }

        return new VerticesMap($ret);
    }

    public function getEdges()
    {
        $ret = array();
        foreach ($this->edgeMap as $edges) {
            foreach ($edges as $edge) {
                if (!in_array($edge, $ret, true)) {
                    $ret []= $edge;
                }
            }
        }

        return new Edges($ret);
    }
}
