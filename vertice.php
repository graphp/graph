<?php

include_once 'edge.php';
include_once 'algorithSearchDepthFirst.php';

class Vertice{
	private $id = NULL;
	private $edges = array();
	
	/**
	 * Creates a Vertice
	 * @param Identifier (int, string, what you want) $id
	 * @param optional array of id's of edges $edges
	 */
	public function __construct($id, $edges = NULL){
		$this->id = $id;
		$this->edges = $edges;
	}
	
//getter setter
	
	/**
	 * returns id of this Vertice
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Add an edge to the Vertice
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
	 * removes an Edge of this Vertice
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
	 * returns all edges of this Vertice
	 */
	public function getEdgeIdArray(){
		return $this->edges;
	}
}

?>