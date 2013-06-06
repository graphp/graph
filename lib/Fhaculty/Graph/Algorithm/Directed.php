<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Set;
use Fhaculty\Graph\Algorithm\Base;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Walk;

/**
 * Basic algorithms for working with the undirected or directed Graphs (digraphs) / Walks.
 *
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Direction
 * @link http://en.wikipedia.org/wiki/Digraph_%28mathematics%29
 */
class Directed extends Base
{
    /**
     * Graph/Walk to operate on
     *
     * @var Set
     */
    private $set;

    /**
     * instanciate new directed algorithm
     *
     * @param Set|Graph|Walk $graphOrWalk either the Graph or Walk to operate on (or the common base class Set)
     */
    public function __construct(Set $graphOrWalk)
    {
        $this->set = $graphOrWalk;
    }

    /**
     * checks whether the graph has any directed edges (aka digraph)
     *
     * @return boolean
     */
    public function isDirected()
    {
        foreach ($this->set->getEdges() as $edge) {
            if ($edge instanceof EdgeDirected) {
                return true;
            }
        }

        return false;
    }
}
