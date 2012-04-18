<?php

abstract class Edge{
	
    /**
     * weight of this edge
     * 
     * @var float|NULL
     * @see Edge::getWeight()
     */
	protected $weight = NULL;
	
	/**
	 * get Vertices that are a target of this edge
	 *
	 * @return array[Vertex]
	 */
	abstract public function getTargetVertices();
	
	/**
	 * Return string with edge visualisation
	 *
	 * @return string
	 */
	abstract function toString();
	
// 	abstract public function hasVertexTo($vertex);
	
// 	abstract public function hasVertexFrom($vertex);
	
	abstract public function isConnection($from, $to);
	
// 	abstract public function getVerticesTo();
	
	/**
	 * get target vertex we can reach with this edge from the given start vertex
	 *
	 * @param Vertex $startVertex
	 * @return Vertex
	 * @throws Exception if given $startVertex is not a valid start
	 * @see Edge::hasEdgeFrom() to check if given start is valid
	 */
	abstract public function getVertexToFrom($startVertex);
	
	/**
	 * get start vertex which can reach us(the given end vertex) with this edge
	 *
	 * @param Vertex $startVertex
	 * @return Vertex
	 * @throws Exception if given $startVertex is not a valid start
	 * @see Edge::hasEdgeFrom() to check if given start is valid
	 */
	abstract public function getVertexFromTo($endVertex);
	
// 	abstract public function getVerticesFrom();
    
	/**
	 * return weight of edge
	 * 
	 * @return float|NULL weight of edge or NULL=not set
	 */
	public function getWeight(){
	    return $this->weight;
	}
	
	/**
	 * set new weight for edge
	 * 
	 * @param float|NULL $weight
	 * @return Edge $this (chainable)
	 */
	public function setWeight($weight){
	    $this->weight = $weight;
	    return $this;
	}
	
	/**
	 * get all vertices this edge connects
	 * 
	 * @return array[Vertex]
	 */
	abstract public function getVertices();
}
