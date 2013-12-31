<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Algorithm\ShortestPath\SingleSource\Result;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\VerticesMap;

class ResultFromVertexPredecessors implements Result
{
    private $vertex;
    private $predecessors;
    private $edges;

    public function __construct(Vertex $vertex, array $predecessors)
    {
        $this->vertex = $vertex;
        $this->predecessors = $predecessors;
        $this->edges = $this->getEdgesCheapestPredecesor();
    }

    public function getWalkTo(Vertex $endVertex)
    {
        return Walk::factoryFromEdges($this->getEdgesTo($endVertex), $this->vertex);
    }

    public function getEdgesTo(Vertex $endVertex)
    {
        return $this->getEdgesToInternal($endVertex, $this->getEdges());
    }

    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param  Vertex       $endVertex
     * @param  Edges|Edge[] $edges     set or array of all input edges to operate on
     * @throws OutOfBoundsException if there's no path to the given vertex
     * @return Edges
     * @uses self::getEdges() if no edges were given
     */
    protected function getEdgesToInternal(Vertex $endVertex, $edges)
    {
        $currentVertex = $endVertex;
        $path = array();
        do {
            $pre = NULL;
            // check all edges to search for edge that points TO current vertex
            foreach ($edges as $edge) {
                try {
                    // get start point of this edge (fails if current vertex is not its end point)
                    $pre = $edge->getVertexFromTo($currentVertex);
                    $path []= $edge;
                    $currentVertex = $pre;
                    break;
                } catch (InvalidArgumentException $ignore) {
                } // ignore: this edge does not point TO current vertex
            }
            if ($pre === NULL) {
                throw new OutOfBoundsException('No edge leading to vertex');
            }
        } while ($currentVertex !== $this->vertex);

        return new Edges(array_reverse($path));
    }

    public function getVertices()
    {
        $graph = $this->vertex->getGraph();
        $vertices = array();

        foreach ($this->predecessors as $vid => $unused) {
            $vertices[$vid] = $graph->getVertex($vid);
        }

        return new VerticesMap($vertices);
    }

    public function hasWalkTo(Vertex $vertex)
    {
        return (isset($this->predecessors[$vertex->getId()]) && $this->vertex->getGraph() === $vertex->getGraph());
    }

    public function getDistanceMap()
    {
        $ret = array();
        foreach ($this->vertex->getGraph()->getVertices()->getMap() as $vid => $vertex) {
            try {
                $ret[$vid] = $this->getDistanceTo($vertex);
            } catch (OutOfBoundsException $ignore) {
            } // ignore vertices that can not be reached
        }

        return $ret;
    }

    public function getDistanceTo(Vertex $endVertex)
    {
        return $this->sumEdges($this->getEdgesTo($endVertex));
    }

    public function createGraph()
    {
        return $this->vertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * get cheapest edges (lowest weight) for given map of vertex predecessors
     *
     * @return Edges
     * @uses Graph::getVertices()
     * @uses Vertex::getEdgesTo()
     * @uses Edges::getEdgeOrder()
     */
    protected function getEdgesCheapestPredecesor()
    {
        $vertices = $this->vertex->getGraph()->getVertices()->getMap();

        $edges = array();
        foreach ($vertices as $vid => $vertex) {
            if (isset($this->predecessors[$vid])) {
                // get predecor
                $predecesVertex = $this->predecessors[$vid];

                // get cheapest edge
                $edges []= $predecesVertex->getEdgesTo($vertex)->getEdgeOrder(Edges::ORDER_WEIGHT);
            }
        }

        return new Edges($edges);
    }

    /**
     * get sum of weight of given edges
     *
     * @param  Edges $edges
     * @return float
     * @uses Edge::getWeight()
     */
    private function sumEdges(Edges $edges)
    {
        $sum = 0;
        foreach ($edges as $edge) {
            $sum += $edge->getWeight();
        }

        return $sum;
    }
}
