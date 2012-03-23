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
}

?>