<?php

abstract class Edge{
	private $id = NULL;
	private $from = NULL;
	private $to = NULL;

//	public function __construct($id){
//		$this->id = $id;
//	}
	
	/**
	 * creats a new Edge
	 * @param identifier of new Edge $id
	 * @param identifier of Vertice $from
	 * @param identifier ofs Vertice $to
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
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * sets the Vertices of this Edge
	 * @param id of new Vertice $from
	 * @param id of new Vertice $to
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
