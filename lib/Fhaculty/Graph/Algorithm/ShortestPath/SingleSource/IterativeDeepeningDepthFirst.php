<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Vertex;

/**
 * @author Fylax
 */
class IterativeDeepeningDepthFirst extends DepthLimited {

    /**
     * 
     * @param \Fhaculty\Graph\Vertex $goal
     * @param int $depth
     * @return \Fhaculty\Graph\Walk
     * @throws \Fhaculty\Graph\Exception\OutOfBoundsException
     */
    public function getWalkTo(Vertex $goal, $depth) {
        for ($i = 1; $i <= $depth; $i++) {
            $this->visited = [];
            $this->vertices = [];
            $this->search($this->vertex, $goal, $i);
            if ($this->found) {
                break;
            }
        }
        if ($this->found) {
            $this->vertices = array_reverse($this->vertices);
            $this->vertices[] = $goal;
            $this->removeDupes();
            return \Fhaculty\Graph\Walk::factoryFromVertices($this->vertices);
        } else {
            throw new \Fhaculty\Graph\Exception\OutOfBoundsException();
        }
    }

}
