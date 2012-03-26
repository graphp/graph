<?php

class Vertex{
	private $id = NULL;
	private $edges = array();
	
	/**
	 * Creates a Vertex
	 * @param Identifier (int, string, what you want) $id
	 * @param optional array of id's of edges $edges
	 */
	public function __construct($id, $edges = NULL){
		$this->id = $id;
		$this->edges = $edges;
	}
	
//getter setter
	
	/**
	 * returns id of this Vertex
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Add an edge to the Vertex
	 * @param id of edge $edgeId
	 * @throws Exception
	 */
	public function addEdgeId($edgeId){
		$edgeId = (int)$edgeId;
		if ( isset($this->edges[ $edgeId ]) ){
			throw new Exception("Edge is allready added");
		}
		
		$this->edges[$edgeId] = $edgeId;
	}
	
	/**
	 * removes an Edge of this Vertex
	 * @param instance of Edge  (EdgeDirected or EdgeUndirected) $edge
	 * @throws Exception
	 */
	public function removeEdgeId($edge){
	
		if ( ! isset($this->edges[ $edgeId ]) ){
			throw new Exception("Edge isn't added");
		}
	
		return $this->edges[$edgeId];
	}
	
	/**
	 * returns all edges of this Vertex
	 */
	public function getEdgeIdArray(){
		return $this->edges;
	}
}
