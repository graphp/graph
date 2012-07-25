<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exception\LogicException;

class Walk{
    /**
     * 
     * @var array[Edge]
     */
    private $edges;
    
    /**
     * 
     * @var array[Vertex]
     */
    private $vertices;
    
    private function __construct(){
        // ...
    }
    
    /**
     * checks whether walk is a cycle (i.e. source vertex = target vertex)
     * 
     * @return bool
     */
    public function isCycle(){
        return (reset($this->vertices) === end($this->vertices));
    }
    
    /**
     * checks whether walk is a path (i.e. does not contain any duplicate edges)
     * 
     * @return bool
     * @uses Walk::hasDuplicates()
     */
    public function isPath(){
        return $this->hasDuplicates($this->edges);
    }
    
    /**
     * checks whether walk contains a cycle (i.e. contains a duplicate vertex)
     * 
     * a walk that CONTAINS a cycle does not neccessarily have to BE a cycle
     * 
     * @return bool
     * @uses Walk::hasDuplicates()
     */
    public function hasCycle(){
        return $this->hasDuplicates($this->vertices);
    }
    
    /**
     * get length of walk (number of edges)
     * 
     * @return int
     */
    public function getLength(){
        return count($this->edges);
    }
    
    /**
     * get total weight of walk (sum all edges' weights)
     *
     * @return float
     * @uses Edge::getWeight()
     */
    public function getWeight(){
    	$sum = 0;
    	foreach($this->edges as $edge){
    		$sum += $edge->getWeight();
    	}
    	return $sum;
    }
    
    /**
     * checks whether ths given array contains duplicate identical entries
     * 
     * @param array $array
     * @return bool
     */
    private function hasDuplicates($array){
        $compare = array();
        foreach($array as $element){
        	if(in_array($element,$compare,true)){ // duplicate element found
        		return false;
        	}else{
        		$compare [] = $element; // add element to temporary array to check for duplicates
        	}
        }
        return true;
    }
    
    /**
     * return original graph
     * 
     * @return Graph
     * @uses Walk::getVertexSource()
     * @uses Vertex::getGraph()
     */
    public function getGraph(){
        return $this->getVertexSource()->getGraph();
    }
    
	/**
	 * create new graph clone with only vertices and edges actually in the walk
	 * 
	 * do not add duplicate vertices and edges for loops and intersections, etc.
	 * 
	 * @return Graph
	 * @uses Walk::getEdges()
	 * @uses Graph::createGraphCloneEdges()
	 */
	public function createGraph(){
	    $graph = $this->getGraph()->createGraphCloneEdges($this->getEdges());   // create new graph clone with only edges of walk
	    $vertices = $this->getVertices();
	    foreach($graph->getVertices() as $vid=>$vertex){                      // get all vertices
	        if(!isset($vertices[$vid])){
	            $vertex->destroy();                                             // remove those not present in the walk (isolated vertices, etc.)
	        }
	    }
	    return $graph;
	}
    
    /**
     * return array of all unique edges of walk
     * 
     * @return array[Edge]
     */
    public function getEdges(){
        $edges = array();
        foreach($this->edges as $edge){
            if(!in_array($edge,$edges,true)){ // filter duplicate edges
                $edges []= $edge;
            }
        }
        return $edges;
    }
    
    /**
     * return array/list of all edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * @return array[Edge]
     */
    public function getEdgesSequence(){
        return $this->edges;
    }
    
    /**
     * return array of all unique vertices of walk
     * 
     * @return array[Vertex]
     */
    public function getVertices(){
        $vertices = array();
        foreach($this->vertices as $vertex){
            $vertices[$vertex->getId()] = $vertex;
        }
        return $vertices;
    }
    
    /**
     * return array/list of all vertices of walk (in sequence visited in walk, may contain duplicates)
     * 
     * @return array[Vertex]
     */
    public function getVerticesSequence(){
        return $this->vertices;
    }
    
    /**
     * return source vertex (first vertex of walk)
     * 
     * @return Vertex
     */
    public function getVertexSource(){
        return reset($this->vertices);
    }
    
    /**
     * return target vertex (last vertex of walk)
     * 
     * @return Vertex
     */
    public function getVertexTarget(){
        return end($this->vertices);
    }
}
