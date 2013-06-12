<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\BaseDirected as DirectedTree;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Search\Base as Search;
use Fhaculty\Graph\Vertex;

/**
 *
 * @link http://en.wikipedia.org/wiki/Spaghetti_stack
 * @see OutTree
 */
class InTree extends DirectedTree
{
    const DIRECTION_CHILDREN = Search::DIRECTION_REVERSE;

    public function getVerticesChildren(Vertex $vertex)
    {
        return $vertex->getVerticesEdgeFrom();
    }

    protected function getVerticesParent(Vertex $vertex)
    {
        return $vertex->getVerticesEdgeTo();
    }
}
