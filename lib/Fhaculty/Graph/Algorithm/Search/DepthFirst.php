<?php

namespace Fhaculty\Graph\Algorithm\Search;

use Fhaculty\Graph\Vertex;

class DepthFirst extends Base{

    /**
     *
     * calculates the recursive algorithm
     *
     * fills $this->visitedVertices
     *
     * @param Vertex $vertex
     */
    private function recursiveDepthFirstSearch(Vertex $vertex,array & $visitedVertices){

        if ( ! isset($visitedVertices[$vertex->getId()]) ){                        //    If I didn't visited this vertex before
            $visitedVertices[$vertex->getId()] = $vertex;                        //        Add Vertex to already visited vertices
                
            $nextVertices = $vertex->getVerticesEdgeTo();                        //        Get next vertices
                
            foreach ($nextVertices as $nextVertix){
                $this->recursiveDepthFirstSearch($nextVertix, $visitedVertices);//            recursive call for next vertices
            }
        }
    }

    private function iterativeDepthFirstSearch(Vertex $vertex){
        $visited = array();
        $todo = array($vertex);
        while($vertex = array_shift($todo)){
            if(!isset($visited[$vertex->getId()])){
                $visited[$vertex->getId()] = $vertex;
                
                foreach(array_reverse($this->getVerticesAdjacent($vertex),true) as $vid=>$nextVertex){
                    $todo[] = $nextVertex;
                }
            }
        }
        return $visited;
    }
    
    /**
     *
     * calculates a recursive depth-first search
     *
     * @return array[Vertex]
     */
    public function getVertices(){
        return $this->iterativeDepthFirstSearch($this->startVertex);
        
        $visitedVertices = array();
        $this->recursiveDepthFirstSearch($this->startVertex, $visitedVertices);
        return $visitedVertices;
    }
}
