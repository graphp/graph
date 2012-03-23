<?php

include 'edge.php';

class Graph{
	private $edges = array();

	private $vertices = array();


//getter setter
	
	public function addEdge($edge){
		$this->edges[$edge->getId()] = $edge;
	}
	
	public function addEdgeDirected($id=NULL){
		$edge = new EdgeDirected($id);
		$this->edges[$edge->getId()] = $edge;
	
		return $edge;
	}
	
	public function addEdgeUndirected($id=NULL){
		$edge = new EdgeUndirected($id);
		$this->edges[$edge->getId()] = $edge;
	
		return $edge;
	}
	
	//returns edge with id = $id
	public function getEdge($id){
		if(!isset($edges[$id])){
			throw new Exception();
		}
		return $edges[$id];
	}
	
	public function getEdges(){
		return $this->edges;
	}
	
	public function addVertice($vertice){
		$this->vertices[$vertice->getId()] = $vertice;
	}
	
	//@clue Wie geht es mit überladen bei unterschiedlichem Datentyp????
//	public function addVertice($id = NULL){
//		$vertice = new Vertice($id);
//		$this->vertices[$vertice->getId()] = $vertice;
//	}
	
	public function getVertice($id){
		if( ! isset($this->vertices[$id]) ){
			throw new Exception();
		}
		
		return $this->vertices[$id];
	}
	
	public function getVertices(){
		return $this->vertices;
	}

//transform methods
	
//	public function getMatrixOb(){
//	}

//Encapsulated algorithem
	
//	public function isConsecutive(){
//		$is = 0;
//	}
}

?>
