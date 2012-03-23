<?php

class Vertice{
	private $id = NULL;
	private $edges = array();
	
	public function __construct($id, $edges = NULL){
		$this->id = $id;
		$this->edges = $edges;
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