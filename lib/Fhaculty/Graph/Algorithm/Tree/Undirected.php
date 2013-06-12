<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\Base as Tree;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\Base as Search;
use Fhaculty\Graph\Algorithm\Search\StrictDepthFirst;
use Fhaculty\Graph\Vertex;

/**
 *
 */
class Undirected extends Tree
{
    public function isTree()
    {
        if ($this->graph->isEmpty()) {
            return true;
        }

        // every vertex can represent a root vertex, so just pick one
        $root = $this->graph->getVertexFirst();

        // TODO: recurse $root to get sub-vertices
        $vertices = array();

        return (count($vertices) === $this->graph->getNumberOfVertices());
    }

    /**
     * checks if the given $vertex is a leaf (outermost vertex with exactly one edge)
     *
     * @param Vertex $vertex
     * @return boolean
     * @uses Vertex::getDegree()
     */
    public function isVertexLeaf(Vertex $vertex)
    {
        return ($vertex->getDegree() === 1);
    }

    public function isVertexInternal(Vertex $vertex)
    {
        return ($vertex->getDegree() >= 2);
    }
}
