<?php

include 'edge.php';
include 'edgeUndirected.php';
include 'edgeDirected.php';
include 'Vertice.php';

class Graph{
	private $edges = array();

	private $vertices = array();


//getter setter
	
	/**
	 * adds a new Edge to the Graph
	 * @param instance of the new Edge $edge
	 */
	public function addEdge($edge){
		$this->edges[$edge->getId()] = $edge;
	}
	
	/**
	 * creat a EdgeDirected
	 * adds this to the Graph and returns the created EdgeDirected
	 * @param identifier of the new Edge $id
	 */
	public function addEdgeDirected($id=NULL){
		$edge = new EdgeDirected($id);
		$this->edges[$edge->getId()] = $edge;
	
		return $edge;
	}
	
	/**
	 * creat a EdgeUndirected
	 * adds this to the Graph and returns the created EdgeUndirected
	 * @param identifier of the new Edge $id
	 */
	public function addEdgeUndirected($id=NULL){
		$edge = new EdgeUndirected($id);
		$this->edges[$edge->getId()] = $edge;
	
		return $edge;
	}
	
	/**
	 * returns the Edge with identifier $id
	 * @param identifier of Edge $id
	 * @throws Exception
	 */
	public function getEdge($id){
		if(!isset($edges[$id])){
			throw new Exception();
		}
		return $edges[$id];
	}
	
	/**
	 * returns a array of all Edges
	 */
	public function getEdges(){
		return $this->edges;
	}
	
	/**
	 * adds a Vertice to the Graph
	 * @param instance of Vertice $vertice
	 */
	public function addVertice($vertice){
		$this->vertices[$vertice->getId()] = $vertice;
	}
	
	//@clue Wie geht es mit überladen bei unterschiedlichem Datentyp????
//	public function addVertice($id = NULL){
//		$vertice = new Vertice($id);
//		$this->vertices[$vertice->getId()] = $vertice;
//	}
	
	/**
	 * returns the Vertice with identifier $id
	 * @param identifier of Vertice $id
	 * @throws Exception
	 */
	public function getVertice($id){
		if( ! isset($this->vertices[$id]) ){
			throw new Exception();
		}
		
		return $this->vertices[$id];
	}
	
	/**
	 * returns an array of all Vertices
	 */
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
