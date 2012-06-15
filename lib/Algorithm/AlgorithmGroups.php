<?php

class AlgorithmGroups extends Algorithm{
    /**
     * graph to operate on
     * 
     * @var Group
     */
    private $graph;
    
    /**
     * instanciate algorithm on given graph
     * 
     * @param Graph $graph
     */
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }

    /**
     * count total number of different groups assigned to vertices
     *
     * @return int
     * @uses AlgorithmGroups::getGroups()
     */
    public function getNumberOfGroups(){
        return count($this->getGroups());
    }

    /**
     * get vector of all group numbers
     *
     * @return array[int]
     * @uses Vertex::getGroup()
     */
    public function getGroups(){
        $groups = array();
        foreach($this->graph->getVertices() as $vertex){
            $groups[$vertex->getGroup()] = true;
        }
        return array_keys($groups);
    }

    /**
     * get array of all vertices in the given group
     *
     * @param int $group
     * @return array[Vertex]
     * @uses Vertex::getGroup()
     */
    public function getVerticesGroup($group){
        $vertices = array();
        foreach($this->graph->getVertices() as $vid=>$vertex){
            if($vertex->getGroup() === $group){
                $vertices[$vid] = $vertex;
            }
        }
        return $vertices;
    }
}