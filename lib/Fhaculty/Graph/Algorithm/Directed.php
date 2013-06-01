<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Set;
use Fhaculty\Graph\Algorithm\Base;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;

class Directed extends Base
{
    private $set;

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
