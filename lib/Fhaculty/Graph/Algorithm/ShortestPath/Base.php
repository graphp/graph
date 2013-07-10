<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Algorithm\BaseVertex;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;

abstract class Base extends BaseVertex
{
    /**
     * get walk (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @return Walk
     * @throws Exception when there's no walk from start to end vertex
     * @uses self::getEdgesTo()
     * @uses Walk::factoryFromEdges()
     */
    public function getWalkTo(Vertex $endVertex)
    {
        return Walk::factoryFromEdges($this->getEdgesTo($endVertex), $this->vertex);
    }

    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @throws Exception
     * @return Edge[]
     * @uses AlgorithmSp::getEdges()
     * @uses AlgorithmSp::getEdgesToInternal()
     */
    public function getEdgesTo(Vertex $endVertex)
    {
        return $this->getEdgesToInternal($endVertex, $this->getEdges());
    }

    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @param  array     $edges     array of all input edges to operate on
     * @throws Exception
     * @return Edge[]
     * @uses AlgorithmSp::getEdges() if no edges were given
     */
    protected function getEdgesToInternal(Vertex $endVertex, array $edges)
    {
        $currentVertex = $endVertex;
        $path = array();
        while ($currentVertex !== $this->vertex) {
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
                throw new UnderflowException('No edge leading to vertex');
            }
        }

        return array_reverse($path);
    }

    /**
     * get sum of weight of given edges
     *
     * @param  Edge[] $edges
     * @return float
     * @uses Edge::getWeight()
     */
    private function sumEdges(array $edges)
    {
        $sum = 0;
        foreach ($edges as $edge) {
            $sum += $edge->getWeight();
        }

        return $sum;
    }

    /**
     * get set of all Vertices the given start vertex has a path to
     *
     * @return Vertices
     * @uses AlgorithmSp::getDistanceMap()
     */
    public function getVertices()
    {
        $vertices = array();
        $map = $this->getDistanceMap();
        foreach ($this->vertex->getGraph()->getVertices()->getMap() as $vid => $vertex) {
            if (isset($map[$vid])) {
                $vertices[$vid] = $vertex;
            }
        }

        return new Vertices($vertices);
    }

    /**
     * get map of vertex IDs to distance
     *
     * @return float[]
     * @uses AlgorithmSp::getEdges()
     * @uses AlgorithmSp::getEdgesToInternal()
     * @uses AlgorithmSp::sumEdges()
     */
    public function getDistanceMap()
    {
        $edges = $this->getEdges();
        $ret = array();
        foreach ($this->vertex->getGraph()->getVertices()->getMap() as $vid => $vertex) {
            try {
                $ret[$vid] = $this->sumEdges($this->getEdgesToInternal($vertex, $edges));
            } catch (UnderflowException $ignore) {
            } // ignore vertices that can not be reached
        }

        return $ret;
    }

    /**
     * get distance (sum of weights) between start vertex and given end vertex
     *
     * @param  Vertex    $endVertex
     * @return float
     * @throws Exception if given vertex is invalid or there's no path to given end vertex
     * @uses AlgorithmSp::getEdgesTo()
     * @uses AlgorithmSp::sumEdges()
     */
    public function getDistance(Vertex $endVertex)
    {
        return $this->sumEdges($this->getEdgesTo($endVertex));
    }

    /**
     * create new resulting graph with only edges on shortest path
     *
     * @return Graph
     * @uses AlgorithmSp::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        return $this->vertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    /**
     * get cheapest edges (lowest weight) for given map of vertex predecessors
     *
     * @param  Vertex[] $predecessor
     * @return Edges
     * @uses Graph::getVertices()
     * @uses Vertex::getEdgesTo()
     * @uses Edges::getEdgeOrder()
     */
    protected function getEdgesCheapestPredecesor(array $predecessor)
    {
        $vertices = $this->vertex->getGraph()->getVertices()->getMap();
        // start vertex doesn't have a predecessor
        unset($vertices[$this->vertex->getId()]);

        $edges = array();
        foreach ($vertices as $vid => $vertex) {
            if (isset($predecessor[$vid])) {
                // get predecor
                $predecesVertex = $predecessor[$vid];

                // get cheapest edge
                $edges []= $predecesVertex->getEdgesTo($vertex)->getEdgeOrder(Edges::ORDER_WEIGHT);
            }
        }

        return new Edges($edges);
    }

    /**
     * get all edges on shortest path for this vertex
     *
     * @return Edges
     */
    abstract public function getEdges();
}
