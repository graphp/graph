<?php

abstract class AlgorithmSearch extends Algorithm{
    /**
     *
     * @var Vertex
     */
    protected $startVertex;
    
    public function __construct(Vertex $startVertex){
    	$this->startVertex = $startVertex;
    }
    
    /**
     * get total number of vertices the start vertex is connected to
     * 
     * @return int
     * @uses AlgorithmSearch::getVertices()
     */
    public function getNumberOfVertices(){
    	return count($this->getVertices());
    }
    
    /**
     * get array of all vertices that can be reached from start vertex
     * 
     * @return array[Vertex]
     */
    abstract public function getVertices();
    
    /**
     * get array of all vertices' IDs that can be reached from start vertex
     * 
     * @return array[int]
     * @uses AlgorithmSearch::getVertices()
     */
    public function getVerticesIds(){
    	return array_keys($this->getVertices());
    }
}
