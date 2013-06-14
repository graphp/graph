<?php

namespace Fhaculty\Graph\Algorithm\Search;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;

class BreadthFirst extends Base
{
    /**
     *
     * @return Vertices
     */
    public function getVertices()
    {
        $queue = array($this->vertex);
        // to not add vertices twice in array visited
        $mark = array($this->vertex->getId() => true);
        // visited vertices
        $visited = array();

        do {
            // get first from queue
            $t = array_shift($queue);
            // save as visited
            $visited[$t->getId()]= $t;

            // get next vertices
            $vertices = $this->getVerticesAdjacent($t);
            foreach ($vertices as $id => $vertex) {
                // if not "toughed" before
                if (!isset($mark[$id])) {
                    // add to queue
                    $queue[] = $vertex;
                    // and mark
                    $mark[$id] = true;
                }
            }

        // untill queue is empty
        } while ($queue);

        return new Vertices($visited);
    }
}
