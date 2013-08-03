<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Algorithm\BaseVertex;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;

/**
 * Abstract base class for shortest path algorithms
 *
 * This abstract base class provides the base interface for working with
 * single-source shortest paths (SSSP).
 *
 * The shortest path problem is the problem of finding a path between two
 * vertices such that the sum of the weights of its constituent edges is
 * minimized. The weight of the shortest path is referred to as distance.
 *
 *    A--[10]-------------B---E<--F
 *     \                 /
 *      \--[4]--C--[2]--D
 *
 * In the above pictured graph, the distance (weight of the shortest path)
 * between A and C is 4, and the shortest path between A and B is "A->C->D->B"
 * with a distance (total weight) of 6.
 *
 * In graph theory, it is usually assumed that a path to an unreachable vertex
 * has infinite distance. In the above pictured graph, there's no way path
 * from A to F, i.e. vertex F is unreachable from vertex A because of the
 * directed edge "E <- F" pointing in the opposite direction. This library
 * considers this an Exception instead. So if you're asking for the distance
 * between A and F, you'll receive an OutOfBoundsException instead.
 *
 * In graph theory, it is usually assumed that each vertex has a (pseudo-)path
 * to itself with a distance of 0. In order to produce reliable, consistent
 * results, this library considers this (pseudo-)path to be non-existant, i.e.
 * there's NO "magic" path between A and A. So if you're asking for the distance
 * between A and A, you'll receive an OutOfBoundsException instead. This allows
 * us to check hether there's a real path between A and A (cycle via other
 * vertices) as well as working with loop edges.
 *
 * @link http://en.wikipedia.org/wiki/Shortest_path_problem
 * @link http://en.wikipedia.org/wiki/Tree_%28data_structure%29
 * @see ShortestPath\Dijkstra
 * @see ShortestPath\MooreBellmanFord which also supports negative Edge weights
 * @see ShortestPath\BreadthFirst with does not consider Edge weights, but only the number of hops
 */
abstract class Base extends BaseVertex
{
    /**
     * get walk (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @return Walk
     * @throws OutOfBoundsException if there's no path to the given end vertex
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
     * @throws OutOfBoundsException if there's no path to the given end vertex
     * @return Edge[]
     * @uses self::getEdges()
     * @uses self::getEdgesToInternal()
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
     * @throws OutOfBoundsException if there's no path to the given vertex
     * @return Edge[]
     * @uses self::getEdges() if no edges were given
     */
    protected function getEdgesToInternal(Vertex $endVertex, array $edges)
    {
        $currentVertex = $endVertex;
        $path = array();
        do {
            $pre = NULL;
            // check all edges to search for edge that points TO current vertex
            foreach ($edges as $i => $edge) {
                try {
                    // get start point of this edge (fails if current vertex is not its end point)
                    $pre = $edge->getVertexFromTo($currentVertex);
                    $path []= $edge;
                    $currentVertex = $pre;
                    unset($edges[$i]);
                    break;
                } catch (InvalidArgumentException $ignore) {
                } // ignore: this edge does not point TO current vertex
            }
            if ($pre === NULL) {
                throw new OutOfBoundsException('No edge leading to vertex');
            }
        } while ($currentVertex !== $this->vertex);

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
     * @uses self::getDistanceMap()
     */
    public function getVertices()
    {
        $vertices = array();
        $map = $this->getDistanceMap();
        foreach ($this->vertex->getGraph()->getVertices() as $vid => $vertex) {
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
     * @uses self::getDistanceMap()
     */
    public function getVerticesId()
    {
        return array_keys($this->getDistanceMap());
    }

    /**
     * checks whether there's a path from this start vertex to given end vertex
     *
     * @param  Vertex  $endVertex
     * @return boolean
     * @uses self::getEdgesTo()
     */
    public function hasVertex(Vertex $vertex)
    {
        try {
            $this->getEdgesTo($vertex);
        }
        catch (OutOfBoundsException $e) {
            return false;
        }
        return true;
    }

    /**
     * get map of vertex IDs to distance
     *
     * @return float[]
     * @uses self::getEdges()
     * @uses self::getEdgesToInternal()
     * @uses self::sumEdges()
     */
    public function getDistanceMap()
    {
        $edges = $this->getEdges();
        $ret = array();
        foreach ($this->vertex->getGraph()->getVertices() as $vid => $vertex) {
            try {
                $ret[$vid] = $this->sumEdges($this->getEdgesToInternal($vertex, $edges));
            } catch (OutOfBoundsException $ignore) {
            } // ignore vertices that can not be reached
        }

        return $ret;
    }

    /**
     * get distance (sum of weights) between start vertex and given end vertex
     *
     * @param  Vertex    $endVertex
     * @return float
     * @throws OutOfBoundsException if there's no path to the given end vertex
     * @uses self::getEdgesTo()
     * @uses self::sumEdges()
     */
    public function getDistance(Vertex $endVertex)
    {
        return $this->sumEdges($this->getEdgesTo($endVertex));
    }

    /**
     * create new resulting graph with only edges on shortest path
     *
     * The resulting Graph will always represent a tree with the start vertex
     * being the root vertex.
     *
     * For example considering the following input Graph with equal weights on
     * each edge:
     *
     *     A----->F
     *    / \     ^
     *   /   \   /
     *  /     \ /
     *  |      E
     *  |       \
     *  |        \
     *  B--->C<---D
     *
     * The resulting shortest path tree Graph will look like this:
     *
     *     A----->F
     *    / \
     *   /   \
     *  /     \
     *  |      E
     *  |       \
     *  |        \
     *  B--->C    D
     *
     * Or by just arranging the Vertices slightly different:
     *
     *          A
     *         /|\
     *        / | \
     *       B  E  \->F
     *      /   |
     *  C<-/    D
     *
     * @return Graph
     * @uses self::getEdges()
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
     * @return Edge[]
     * @uses Graph::getVertices()
     * @uses Vertex::getEdgesTo()
     * @uses Edge::getFirst()
     */
    protected function getEdgesCheapestPredecesor(array $predecessor)
    {
        $vertices = $this->vertex->getGraph()->getVertices();

        $edges = array();
        foreach ($vertices as $vid => $vertex) {
            if (isset($predecessor[$vid])) {
                // get predecor
                $predecesVertex = $predecessor[$vid];

                // get cheapest edge
                $edges []= Edge::getFirst($predecesVertex->getEdgesTo($vertex), Edge::ORDER_WEIGHT);
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
