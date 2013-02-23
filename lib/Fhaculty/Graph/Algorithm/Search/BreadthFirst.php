<?php

namespace Fhaculty\Graph\Algorithm\Search;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;

class BreadthFirst extends Base
{
    /**
     *
     * @return Vertex[]
     */
    public function getVertices()
    {
        $queue = array($this->startVertex);
        $mark = array($this->startVertex->getId() => true);                            //to not add vertices twice in array visited
        $visited = array();                                                        //visited vertices

        do {
            $t = array_shift($queue);                                                // get first from queue
            $visited[$t->getId()]= $t;                                                //save as visited

            $vertices = $this->getVerticesAdjacent($t);                                //get next vertices
            foreach ($vertices as $id => $vertex) {
                if (!isset($mark[$id])) {                                                    //if not "toughed" before
                    $queue[] = $vertex;                                                        //add to queue
                    $mark[$id] = true;                                                        //and mark
                }
            }

        } while ($queue);                                                            //untill queue is empty

        return $visited;
    }
}
