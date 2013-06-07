<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;

/**
 * Basic algorithms for working with the undirected or directed Graphs (digraphs) / Walks.
 *
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Direction
 * @link http://en.wikipedia.org/wiki/Digraph_%28mathematics%29
 */
class Directed extends BaseSet
{
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
