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
     * checks whether the input graph's vertex groups are a valid bipartition
     *
     * @return boolean
     * @see AlgorithmBipartit() if you do NOT want to take vertex groups into consideration
     * @uses AlgorithmGroups::getNumberOfGroups()
     * @uses Vertex::getGroup()
     */
    public function isBipartit(){
    	if($this->getNumberOfGroups() !== 2){ // graph has to contain exactly 2 groups
    		return false;
    	}
    
    	foreach($this->graph->getVertices() as $vertex){                      // for each vertex
    		$group = $vertex->getGroup();                                       // get current group
    		foreach($vertex->getVerticesEdge() as $vertexNeighbor){            // for every neighbor vertex
    			if($vertexNeighbor->getGroup() === $group){                     // vertex group must be other group
    				return false;
    			}
    		}
    	}
    
    	return true;
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
