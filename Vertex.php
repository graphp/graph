<?php

class Vertex{
	private $id;
	private $edges;
	
	/**
	 * Creates a Vertex
	 * 
	 * @param int   $id    Identifier (int, string, what you want) $id
	 * @param array $edges optional array of id's of edges
	 */
	public function __construct($id, $edges = array()){
		$this->id = $id;
		$this->edges = $edges;
	}
	
//getter setter
	
	/**
	 * returns id of this Vertex
	 * 
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Add an edge to the Vertex
	 * 
	 * @param int $edgeId id of edge
	 * @return Vertex $this (chainable)
	 * @throws Exception
	 */
	public function addEdgeId($edgeId){
		$edgeId = (int)$edgeId;
		if ( isset($this->edges[ $edgeId ]) ){
			throw new Exception("Edge is allready added");
		}
		
		$this->edges[$edgeId] = $edgeId;
		return $this;
	}
	
	/**
	 * removes an Edge of this Vertex
	 * 
	 * @param int $edge if of Edge  (EdgeDirected or EdgeUndirected)
	 * @return Vertex $this (chainable)
	 * @throws Exception
	 */
	public function removeEdgeId($edge){
	
		if ( ! isset($this->edges[ $edge ]) ){
			throw new Exception("Edge isn't added");
		}
	
		unset($this->edges[$edge]);
		return $this;
	}
	
	/**
	 * returns all edges of this Vertex
	 * 
	 * @return array[int]
	 */
	public function getEdgeIdArray(){
		return $this->edges;
	}
}
