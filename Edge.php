<?php

abstract class Edge{
	private $id;
	private $from;
	private $to;

//	public function __construct($id){
//		$this->id = $id;
//	}
	
	/**
	 * creats a new Edge
	 * 
	 * @param int      $id   identifier of new Edge
	 * @param int|NULL $from identifier of start/source Vertex
	 * @param int|NULL $to   identifier of end/target Vertex
	 */
	public function __construct($id, $from = NULL, $to = NULL){
		$id = (int)$id;
		$from = (int)$from;
		$to = (int)$to;
		
		$this->id = $id;
		$this->from = $from;
		$this->to = $to;
	}
	
//getter setter
	
	/**
	 * returns the id of this Edge
	 * 
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * sets the Vertices of this Edge
	 * @param int $from id of new Vertex
	 * @param int $to   id of new Vertex
	 */
	public function setEdgeIds($from, $to){
		$from = (int)$from;
		$to = (int)$to;
		$this->from = $from;
		$this->to = $to;
	}
	
	public function getFromId(){
		return $this->from;
	}
	
	public function getToId(){
		return $this->to;
	}
}
