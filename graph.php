<?php

class Graph{
	private $edges = array();

	private $vertices = array();


//getter setter
	
	//returns edge with id = $id
	public function getEdge($id){
		if(!isset($edges[$id])){
			throw new Exception();
		}
		return $edges[$id];
	}

	public function addEdge($id=NULL){
		return new Edge($id);
	}

//transform methods
	
	public function getMatrixOb(){
	}

//Encapsulated algorithem
	
	public function isConsecutive(){
		$is = 0;
	}
}

?>