<?php

namespace Fhaculty\Graph\Algorithm\Search;

use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;

use Fhaculty\Graph\Exception\DomainException;

use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;

abstract class Base extends AlgorithmBase{
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
            throw new InvalidArgumentException('Invalid direction given');
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
            throw new DomainException('Should not happen. Invalid direction setting');
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
     * @return Vertex[]
     */
    abstract public function getVertices();
    
    /**
     * get array of all vertices' IDs that can be reached from start vertex
     * 
     * @return int[]
     * @uses AlgorithmSearch::getVertices()
     */
    public function getVerticesIds(){
    	return array_keys($this->getVertices());
    }
}
