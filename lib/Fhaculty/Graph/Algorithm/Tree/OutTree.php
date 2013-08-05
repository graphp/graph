<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\BaseDirected as DirectedTree;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Vertex;

/**
 * Usual OutTree implementation where Edges "point away" from root Vertex
 *
 *          ROOT
 *          /  \
 *    A <--/    \--> B
 *                   \
 *                    \--> C
 *
 * also known as arborescence
 *
 * @link http://en.wikipedia.org/wiki/Arborescence_%28graph_theory%29
 * @see DirectedTree for more information on directed, rooted trees
 */
class OutTree extends DirectedTree
{
    public function getVerticesChildren(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeTo();
        if ($vertices->hasDuplicates()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }

    protected function getVerticesParent(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeFrom();
        if ($vertices->hasDuplicates()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }
}
