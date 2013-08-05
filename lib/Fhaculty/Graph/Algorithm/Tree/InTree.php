<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\BaseDirected as DirectedTree;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Vertex;

/**
 * Alternative InTree implementation where Edges "point towards" root Vertex
 *
 *         ROOT
 *         ^  ^
 *        /    \
 *       A      B
 *              ^
 *               \
 *                C
 *
 * @link http://en.wikipedia.org/wiki/Spaghetti_stack
 * @see DirectedTree for more information on directed, rooted trees
 */
class InTree extends DirectedTree
{
    public function getVerticesChildren(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeFrom();
        if ($vertices->hasDuplicates()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }

    protected function getVerticesParent(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeTo();
        if ($vertices->hasDuplicates()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }
}
