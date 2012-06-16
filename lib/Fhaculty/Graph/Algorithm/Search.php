<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Vertex;
use \Exception;

abstract class Search extends Base{
    /**
     *
     * @var Vertex
     */
    protected $startVertex;
    
    const DIRECTION_FORWARD = 0;
    const DIRECTION_REVERSE = 1;
    const DIRECTION_BOTH = 2;
    
    private $direction = self::DIRECTION_FORWARD;
    
    public function __construct(Vertex $startVertex){
    	$this->startVertex = $startVertex;
    }
    
    /**
     * set direction in which to follow adjacent vertices
     * 
     * @param int $direction
     * @return AlgorithmSearch $this (chainable)
     * @throws Exception
     * @see AlgorithmSearch::getVerticesAdjacent()
     */
    public function setDirection($direction){
        if($direction !== self::DIRECTION_FORWARD && $direction !== self::DIRECTION_REVERSE && $direction !== self::DIRECTION_BOTH){
            throw new Exception('Invalid direction given');
        }
        $this->direction = $direction;
        return $this;
    }
    
    protected function getVerticesAdjacent(Vertex $vertex){
        if($this->direction === self::DIRECTION_FORWARD){
            return $vertex->getVerticesEdgeTo();
        }else if($this->direction === self::DIRECTION_REVERSE){
            return $vertex->getVerticesEdgeFrom();
        }else if($this->direction === self::DIRECTION_BOTH){
            return $vertex->getVerticesEdge();
        }else{
            throw new Exception('Invalid direction setting');
        }
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
