<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Vertex;

/**
 * @author Fylax
 */
class DepthLimited extends Base {

    /** @var array Contains an ordered list of vertices of the walk */
    protected $vertices;
    /** @var bool Wether if a path has been found */
    protected $found = false;
    /** @var array Remembers already visited vertices. */
    protected $visited;

    /**
     * 
     * @param \Fhaculty\Graph\Vertex $goal
     * @param int $depth
     * @return \Fhaculty\Graph\Walk
     * @throws \Fhaculty\Graph\Exception\OutOfBoundsException
     */
    public function getWalkTo(Vertex $goal, $depth) {
        $this->search($this->vertex, $goal, $depth);
        if ($this->found) {
            $this->vertices = array_reverse($this->vertices);
            $this->vertices[] = $goal;
            $this->removeDupes();
            return \Fhaculty\Graph\Walk::factoryFromVertices($this->vertices);
        } else {
            throw new \Fhaculty\Graph\Exception\OutOfBoundsException();
        }
    }

    /**
     * 
     * @param \Fhaculty\Graph\Algorithm\Search\Vertex $node
     * @param \Fhaculty\Graph\Algorithm\Search\Vertex $goal
     * @param int $depth
     */
    protected function search(Vertex $node, Vertex $goal, $depth) {
        if ($depth >= 0 and !$this->found) {
            if ($node->getId() == $goal->getId()) {
                $this->found = true;
                return;
            }
            foreach ($node->getVerticesEdgeTo()->getVerticesDistinct() as $child) {
                if (!isset($this->visited[$child->getId()])) {
                    $this->visited[$child->getId()] = true;
                    $this->search($child, $goal, $depth - 1);
                    $this->vertices[] = $node;
                }
            }
        } 
    }

    protected function removeDupes() {
        for ($i = 1; $i < count($this->vertices); $i++) {
            if (isset($this->vertices[$i - 1]) and $this->vertices[$i - 1]->getId() == $this->vertices[$i]->getId()) {
                unset($this->vertices[$i - 1]);
            }
        }
        $this->vertices = array_values($this->vertices);
    }

    public function getEdges() {
        for ($i = 0; $i < count($this->vertices) - 1; $i++) {
            $edges[] = new \Fhaculty\Graph\Edge\Directed($this->vertices[$i], $this->vertices[i + 1]);
        }
        return new \Fhaculty\Graph\Set\Edges($edges);
    }

}
