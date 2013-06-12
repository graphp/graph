<?php

namespace Fhaculty\Graph\Algorithm\Tree;

use Fhaculty\Graph\Algorithm\Tree\BaseDirected as DirectedTree;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Vertex;

/**
 * A rooted tree with the "away from root" direction (a more narrow term is an "arborescence"), meaning:
 *
 * @link http://en.wikipedia.org/wiki/Arborescence_%28graph_theory%29
 * @see InTree
 */
class OutTree extends DirectedTree
{
    public function getVerticesChildren(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeTo();
        if (count($vertices) !== $vertex->getDegreeOut()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }

    protected function getVerticesParent(Vertex $vertex)
    {
        $vertices = $vertex->getVerticesEdgeFrom();
        if (count($vertices) !== $vertex->getDegreeIn()) {
            throw new UnexpectedValueException();
        }

        return $vertices;
    }
}
