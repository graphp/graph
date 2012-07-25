<?php

namespace Fhaculty\Graph;

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
    
    /**
     * 
     * @var Vertex
     */
    private $vertexSource;
    
    private function __construct(){
        // ...
    }
    
    public function isCycle(){
        return ($this->vertexStart === $this->vertexTarget);
    }
    
    public function isPath(){
        return $this->hasDuplicates($this->edges);
    }
    
    public function hasCycle(){
        return $this->hasDuplicates($this->vertices);
    }
    
    public function getLength(){
        return count($this->edges);
    }
    
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
    
    public function createGraph(){
        // TODO:
        $graph = $this->vertexSource->createGraphClone(); // ...
        // ...
        
        //
        
        return $graph;
    }
    
    public function getEdges(){
        return $this->edges;
    }
    
    public function getVertices(){
        return $this->vertices;
    }
    
    public function getVertexSource(){
        return $this->vertexSource;
    }
    
    public function getVertexTarget(){
        return $this->vertexTarget;
    }
}
