<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Walk;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Exception\InvalidArgumentException;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;

abstract class Base extends AlgorithmBase
{
    /**
     * start vertex to build shortest paths to
     *
     * @var Vertex
     */
    protected $startVertex;

    public function __construct(Vertex $startVertex)
    {
        $this->startVertex = $startVertex;
    }

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
        return Walk::factoryFromEdges($this->getEdgesTo($endVertex), $this->startVertex);
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
        return $this->getEdgesToInternal($endVertex,$this->getEdges());
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
    protected function getEdgesToInternal(Vertex $endVertex,array $edges)
    {
        $currentVertex = $endVertex;
        $path = array();
        while ($currentVertex !== $this->startVertex) {
            $pre = NULL;
            foreach ($edges as $edge) { // check all edges to search for edge that points TO current vertex
                try {
                    $pre = $edge->getVertexFromTo($currentVertex); // get start point of this edge (fails if current vertex is not its end point)
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
     * get array of all vertices the given start vertex has a path to
     *
     * @return Vertex[]
     * @uses AlgorithmSp::getDistanceMap()
     */
    public function getVertices()
    {
        $vertices = array();
        $map = $this->getDistanceMap();
        foreach ($this->startVertex->getGraph()->getVertices() as $vid=>$vertex) {
            if (isset($map[$vid])) {
                $vertices[$vid] = $vertex;
            }
        }

        return $vertices;
    }

    /**
     * get array of all vertices' IDs the given start vertex has a path to
     *
     * @return int[]
     * @uses AlgorithmSp::getDistanceMap()
     */
    public function getVerticesId()
    {
        return array_keys($this->getDistanceMap());
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
        foreach ($this->startVertex->getGraph()->getVertices() as $vid=>$vertex) {
            try {
                $ret[$vid] = $this->sumEdges($this->getEdgesToInternal($vertex,$edges));
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
        return $this->startVertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }

    /**
     * get cheapest edges (lowest weight) for given map of vertex predecessors
     *
     * @param  Vertex[] $predecessor
     * @return Edge[]
     * @uses Graph::getVertices()
     * @uses Vertex::getEdgesTo()
     * @uses Edge::getFirst()
     */
    protected function getEdgesCheapestPredecesor(array $predecessor)
    {
        $vertices = $this->startVertex->getGraph()->getVertices();
        unset($vertices[$this->startVertex->getId()]);                          //start vertex doesn't have a predecessor

        $edges = array();
        foreach ($vertices as $vid=>$vertex) {
            //echo $vertex->getId()." : ".$this->startVertex->getId()."\n";
            if (isset( $predecessor[$vid] )) {
                $predecesVertex = $predecessor[$vid];    //get predecor

                //echo "EDGE FROM ".$predecesVertex->getId()." TO ".$vertex->getId()." WITH COSTS: ".$totalCostOfCheapestPathTo[$vertex->getId()]."\n";

                $edges []= Edge::getFirst($predecesVertex->getEdgesTo($vertex),Edge::ORDER_WEIGHT);    //get cheapest edge
            }
        }

        return $edges;
    }

    /**
     * get all edges on shortest path for this vertex
     *
     * @return Edge[]
     */
    abstract public function getEdges();
}
