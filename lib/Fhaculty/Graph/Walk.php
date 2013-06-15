<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\VerticesAggregate;

/**
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 */
class Walk extends Set implements VerticesAggregate
{
    /**
     * construct new walk from given start vertex and given array of edges
     *
     * @param  array                $edges
     * @param  Vertex               $startVertex
     * @return \Fhaculty\Graph\Walk
     */
    public static function factoryFromEdges(array $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex);
        $vertexCurrent = $startVertex;
        foreach ($edges as $edge) {
            $vertexCurrent = $edge->getVertexToFrom($vertexCurrent);
            $vertices []= $vertexCurrent;
        }

        return new self($vertices, $edges);
    }

    protected function __construct(array $vertices, array $edges)
    {
        $this->vertices = $vertices;
        $this->edges    = $edges;
    }

    /**
     * checks whether walk is a cycle (i.e. source vertex = target vertex)
     *
     * @return bool
     * @link http://en.wikipedia.org/wiki/Cycle_%28graph_theory%29
     */
    public function isCycle()
    {
        return (reset($this->vertices) === end($this->vertices));
    }

    /**
     * checks whether walk is a path (i.e. does not contain any duplicate edges)
     *
     * @return bool
     * @uses Walk::hasArrayDuplicates()
     */
    public function isPath()
    {
        return !$this->hasArrayDuplicates($this->edges);
    }

    /**
     * checks whether walk contains a cycle (i.e. contains a duplicate vertex)
     *
     * a walk that CONTAINS a cycle does not neccessarily have to BE a cycle
     *
     * @return bool
     * @uses Walk::hasArrayDuplicates()
     * @see Walk::isCycle()
     */
    public function hasCycle()
    {
        return $this->hasArrayDuplicates($this->vertices);
    }

    /**
     * checks whether this walk is a loop (single edge connecting vertex A with vertex A again)
     *
     * @return boolean
     * @uses Walk::isCycle()
     */
    public function isLoop()
    {
        return (count($this->edges) === 1 && $this->isCycle());
    }

    /**
     * checks whether this walk is a digon (a pair of parallel edges in a multigraph or a pair of antiparallel edges in a digraph)
     *
     * a digon is a cycle connecting exactly two distinct vertices with exactly
     * two distinct edges.
     *
     * @return boolean
     * @uses Walk::hasArrayDuplicates()
     * @uses Walk::getVertices()
     * @uses Walk::isCycle()
     */
    public function isDigon()
    {
        // exactly 2 edges
        return (count($this->edges) === 2 &&
                // no duplicate edges
                !$this->hasArrayDuplicates($this->edges) &&
                // exactly two distinct vertices
                count($this->getVertices()) === 2 &&
                // this is actually a cycle
                $this->isCycle());
    }

    /**
     * checks whether this walk is a triangle (a simple cycle with exactly three distinct vertices)
     *
     * @return boolean
     * @uses Walk::getVertices()
     * @uses Walk::isCycle()
     */
    public function isTriangle()
    {
        // exactly 3 (implicitly distinct) edges
        return (count($this->edges) === 3 &&
                // exactly three distinct vertices
                count($this->getVertices()) === 3 &&
                // this is actually a cycle
                $this->isCycle());
    }

    /**
     * check whether this walk is simple
     *
     * contains no duplicate/repeated vertices (and thus no duplicate edges either)
     * other than the starting and ending vertices of cycles.
     *
     * @return boolean
     * @uses Walk::isCycle()
     * @uses Walk::hasArrayDuplicates()
     */
    public function isSimple()
    {
        $vertices = $this->vertices;
        // ignore starting vertex for cycles as it's always the same as ending vertex
        if ($this->isCycle()) {
            unset($vertices[0]);
        }

        return !$this->hasArrayDuplicates($vertices);
    }

    /**
     * checks whether walk is hamiltonian (i.e. walk over ALL VERTICES of the graph)
     *
     * @return boolean
     * @see Walk::isEulerian() if you want to check for all EDGES instead of VERTICES
     * @uses Walk::isArrayContentsEqual()
     * @link http://en.wikipedia.org/wiki/Hamiltonian_path
     */
    public function isHamiltonian()
    {
        return $this->isArrayContentsEqual($this->getVertices()->getVector(), $this->getGraph()->getVertices()->getVector());
    }

    /**
     * checks whether walk is eulerian (i.e. a walk over ALL EDGES of the graph)
     *
     * @return boolean
     * @see Walk::isHamiltonian() if you want to check for all VERTICES instead of EDGES
     * @uses Walk::isArrayContentsEqual()
     * @link http://en.wikipedia.org/wiki/Eulerian_path
     */
    public function isEulerian()
    {
        return $this->isArrayContentsEqual($this->edges, $this->getGraph()->getEdges());
    }

    /**
     * return original graph
     *
     * @return Graph
     * @uses Walk::getVertexSource()
     * @uses Vertex::getGraph()
     */
    public function getGraph()
    {
        return $this->getVertexSource()->getGraph();
    }

    /**
     * create new graph clone with only vertices and edges actually in the walk
     *
     * do not add duplicate vertices and edges for loops and intersections, etc.
     *
     * @return Graph
     * @uses Walk::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function createGraph()
    {
        // create new graph clone with only edges of walk
        $graph = $this->getGraph()->createGraphCloneEdges($this->getEdges());
        $vertices = $this->getVertices()->getMap();
        // get all vertices
        foreach ($graph->getVertices()->getMap() as $vid => $vertex) {
            if (!isset($vertices[$vid])) {
                // remove those not present in the walk (isolated vertices, etc.)
                $vertex->destroy();
            }
        }

        return $graph;
    }

    /**
     * return array of all unique edges of walk
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        $edges = array();
        foreach ($this->edges as $edge) {
            // filter duplicate edges
            if (!in_array($edge, $edges, true)) {
                $edges []= $edge;
            }
        }

        return $edges;
    }

    /**
     * return array/list of all edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return Edge[]
     */
    public function getEdgesSequence()
    {
        return $this->edges;
    }

    /**
     * return set of all unique Vertices of walk
     *
     * @return Vertices
     */
    public function getVertices()
    {
        $vertices = array();
        foreach ($this->vertices as $vertex) {
            $vertices[$vertex->getId()] = $vertex;
        }

        return new Vertices($vertices);
    }

    /**
     * return set of all Vertices of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return Vertices
     */
    public function getVerticesSequence()
    {
        return new Vertices($this->vertices);
    }

    /**
     * return array of all vertex ids of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return string[]
     * @uses Vertex::getId()
     */
    public function getVerticesSequenceId()
    {
        return $this->getVerticesSequence()->getIds();
    }

    /**
     * get IDs of all vertices in the walk
     *
     * @return int[]
     */
    public function getVerticesId()
    {
        return $this->getVertices()->getIds();
    }

    /**
     * return source vertex (first vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexSource()
    {
        return reset($this->vertices);
    }

    /**
     * return target vertex (last vertex of walk)
     *
     * @return Vertex
     */
    public function getVertexTarget()
    {
        return end($this->vertices);
    }

    /**
     * get alternating sequence of vertex, edge, vertex, edge, ..., vertex
     *
     * @return array
     */
    public function getAlternatingSequence()
    {
        $ret = array();
        for ($i = 0, $l = count($this->edges); $i < $l; ++$i) {
            $ret []= $this->vertices[$i];
            $ret []= $this->edges[$i];
        }
        $ret[] = $this->vertices[$i];

        return $ret;
    }

    /**
     * checks whether ths given array contains duplicate identical entries
     *
     * @param  array $array
     * @return bool
     */
    private function hasArrayDuplicates($array)
    {
        $compare = array();
        foreach ($array as $element) {
            // duplicate element found
            if (in_array($element, $compare, true)) {
                return true;
            } else {
                // add element to temporary array to check for duplicates
                $compare [] = $element;
            }
        }

        return false;
    }

    /**
     * checks whether the contents of array a equals those of array b (ignore keys and order but otherwise strict check)
     *
     * @param  array   $a
     * @param  array   $b
     * @return boolean
     */
    private function isArrayContentsEqual($a, $b)
    {
        foreach ($b as $one) {
            $pos = array_search($one, $a, true);
            if ($pos === false) {
                return false;
            } else {
                unset($a[$pos]);
            }
        }

        return $a ? false : true;
    }

    /**
     * check to make sure this walk is still valid (i.e. source graph still contains all vertices and edges)
     *
     * @return boolean
     * @uses Walk::getGraph()
     * @uses Graph::getVertices()
     * @uses Graph::getEdges()
     */
    public function isValid()
    {
        $vertices = $this->getGraph()->getVertices()->getMap();
        // check source graph contains all vertices
        foreach ($this->vertices as $vertex) {
            $vid = $vertex->getId();
            // make sure vertex ID exists and has not been replaced
            if (!isset($vertices[$vid]) || $vertices[$id] !== $vertex) {
                return false;
            }
        }
        $edges = $this->getGraph()->getEdges();
        // check source graph contains all edges
        foreach ($this->edges as $edge) {
            if (!in_array($edge, $edges, true)) {
                return false;
            }
        }

        return true;
    }
}
