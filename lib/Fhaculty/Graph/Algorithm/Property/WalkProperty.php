<?php

namespace Fhaculty\Graph\Algorithm\Property;

use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Algorithm\Base as BaseAlgorithm;
use Fhaculty\Graph\Algorithm\Loop as AlgorithmLoop;

/**
 * Simple algorithms for working with Walk properties
 *
 * @see GraphProperty
 */
class WalkProperty extends BaseAlgorithm
{
    /**
     * the Walk to operate on
     *
     * @var Walk
     */
    protected $walk;

    /**
     * instantiate new WalkProperty algorithm
     *
     * @param Walk $walk
     */
    public function __construct(Walk $walk)
    {
        $this->walk = $walk;
    }

    /**
     * checks whether walk is a cycle (i.e. source vertex = target vertex)
     *
     * A cycle is also known as a closed path, a walk that is NOT a cycle is
     * alsos known as an open path.
     *
     * A walk with no edges is not considered a cycle. The shortest possible
     * cycle is a single loop edge.
     *
     * @return bool
     * @link http://en.wikipedia.org/wiki/Cycle_%28graph_theory%29
     */
    public function isCycle()
    {
        $vertices = $this->walk->getVerticesSequence();
        return (reset($vertices) === end($vertices) && $this->walk->getEdges());
    }

    /**
     * checks whether walk is a path (i.e. does not contain any duplicate edges)
     *
     * A path Walk is also known as a trail.
     *
     * @return bool
     * @uses self::hasArrayDuplicates()
     * @link http://www.proofwiki.org/wiki/Definition:Trail
     */
    public function isPath()
    {
        return !$this->hasArrayDuplicates($this->walk->getEdgesSequence());
    }

    /**
     * checks whether walk contains a cycle (i.e. contains a duplicate vertex)
     *
     * a walk that CONTAINS a cycle does not neccessarily have to BE a cycle
     *
     * @return bool
     * @uses self::hasArrayDuplicates()
     * @see self::isCycle()
     */
    public function hasCycle()
    {
        return $this->hasArrayDuplicates($this->walk->getVerticesSequence());
    }

    /**
     * checks whether this walk IS a loop (single edge connecting vertex A with vertex A again)
     *
     * @return boolean
     * @uses self::isCycle()
     * @see self::hasLoop()
     */
    public function isLoop()
    {
        return ($this->walk->getNumberOfEdges() === 1 && $this->isCycle());
    }

    /**
     * checks whether this walk HAS a look (single edge connecting vertex A with vertex A again)
     *
     * @return boolean
     * @uses AlgorithmLoop::hasLoop()
     * @see self::isLoop()
     */
    public function hasLoop()
    {
        $alg = new AlgorithmLoop($this->walk);

        return $alg->hasLoop();
    }

    /**
     * checks whether this walk is a digon (a pair of parallel edges in a multigraph or a pair of antiparallel edges in a digraph)
     *
     * a digon is a cycle connecting exactly two distinct vertices with exactly
     * two distinct edges.
     *
     * @return boolean
     * @uses self::hasArrayDuplicates()
     * @uses self::isCycle()
     */
    public function isDigon()
    {
        // exactly 2 edges
        return ($this->walk->getNumberOfEdges() === 2 &&
                // no duplicate edges
                !$this->hasArrayDuplicates($this->walk->getEdgesSequence()) &&
                // exactly two distinct vertices
                count($this->walk->getVertices()) === 2 &&
                // this is actually a cycle
                $this->isCycle());
    }

    /**
     * checks whether this walk is a triangle (a simple cycle with exactly three distinct vertices)
     *
     * @return boolean
     * @uses self::isCycle()
     */
    public function isTriangle()
    {
        // exactly 3 (implicitly distinct) edges
        return ($this->walk->getNumberOfEdges() === 3 &&
                // exactly three distinct vertices
                count($this->walk->getVertices()) === 3 &&
                // this is actually a cycle
                $this->isCycle());
    }

    /**
     * check whether this walk is simple
     *
     * contains no duplicate/repeated vertices (and thus no duplicate edges either)
     * other than the starting and ending vertices of cycles.
     *
     * A simple Walk is also known as a chain.
     *
     * @return boolean
     * @uses self::isCycle()
     * @uses self::hasArrayDuplicates()
     */
    public function isSimple()
    {
        $vertices = $this->walk->getVerticesSequence();
        // ignore starting vertex for cycles as it's always the same as ending vertex
        if ($this->isCycle()) {
            unset($vertices[0]);
        }

        return !$this->hasArrayDuplicates($vertices);
    }

    /**
     * checks whether walk is hamiltonian (i.e. walk over ALL VERTICES of the graph)
     *
     * A hamiltonian Walk is also known as a spanning walk.
     *
     * @return boolean
     * @see self::isEulerian() if you want to check for all EDGES instead of VERTICES
     * @uses self::isArrayContentsEqual()
     * @link http://en.wikipedia.org/wiki/Hamiltonian_path
     */
    public function isHamiltonian()
    {
        $vertices = $this->walk->getVerticesSequence();
        // ignore starting vertex for cycles as it's always the same as ending vertex
        if ($this->isCycle()) {
            unset($vertices[0]);
        }
        return $this->isArrayContentsEqual($vertices, $this->walk->getGraph()->getVertices());
    }

    /**
     * checks whether walk is eulerian (i.e. a walk over ALL EDGES of the graph)
     *
     * @return boolean
     * @see self::isHamiltonian() if you want to check for all VERTICES instead of EDGES
     * @uses self::isArrayContentsEqual()
     * @link http://en.wikipedia.org/wiki/Eulerian_path
     */
    public function isEulerian()
    {
        return $this->isArrayContentsEqual($this->walk->getEdgesSequence(), $this->walk->getGraph()->getEdges());
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
}
