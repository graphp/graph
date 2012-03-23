<?php

class Vertice{
	private $id = NULL;
	private $edges = array();
	
	/**
	 * Creates a Vertice
	 * @param Identifier (int, string, what you want) $id
	 * @param array of id's of edges $edges
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
	 * @param instance of Edge (EdgeDirected or EdgeUndirected) $edge
	 * @throws Exception
	 */
	public function addEdge($edge){
		
		if ( isset($this->edges[ $edge->getId() ]) ){
			throw new Exception("Edge is allready added");
		}
		
		$this->edges[$edge->getId()] = $edge;
	}
	
	/**
	 * removes an Edge of this Vertice
	 * @param instance of Edge  (EdgeDirected or EdgeUndirected) $edge
	 * @throws Exception
	 */
	public function removeEdge($edge){
	
		if ( ! isset($this->edges[ $edge->getId() ]) ){
			throw new Exception("Edge isn't added");
		}
	
		return $this->edges[$edge->getId()];
	}
	
	/**
	 * returns the Edge with the identifier $id
	 * @param identifier of Edge $id
	 * @throws Exception
	 */
	public function getEdge($id){
		if ( ! isset($this->edges[$id]) ){
			throw new Exception("Edge isn't added");
		}
		
		return $this->edges[$id];
	}
	
	/**
	 * returns all edges of this Vertice
	 */
	public function getEdges(){
		return $this->edges;
	}
	
//Encapsulated algorithem
	
	//Breadth-first search (prototyp)
	public function searchBreadthFirst(){
		$alg = new BreitenSuche_Agl($this);
		return $alg->getResult();
	}
	
	public function searchDepthFirst(){
		
	} 
}

?>