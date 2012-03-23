<?php

class Vertice{
	private $id = NULL;
	private $edges = array();
	
	//auto search for free id ???
	public function __construct(){
		
	}
	
	public function __construct($id){
		$this->id = $id;
	}
	
//getter setter
	
	public function getId(){
		return $this->id;
	}
	
	public function addEdge($edge){
		
		if ( isset($this->edges[ $edge->getId() ]) ){
			throw new Exception("Edge is allready added");
		}
		
		$this->edges[$edge->getId()] = $edge;
	}
	
	public function removeEdge($edge){
	
		if ( ! isset($this->edges[ $edge->getId() ]) ){
			throw new Exception("Edge isn't added");
		}
	
		return $this->edges[$edge->getId()];
	}
	
	public function getEdge($id){
		if ( ! isset($this->edges[$id]) ){
			throw new Exception("Edge isn't added");
		}
		
		return $this->edges[$id];
	}
	
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