<?php

abstract class Edge{
	private $id = NULL;
	private $from = NULL;
	private $to = NULL;

//	public function __construct($id){
//		$this->id = $id;
//	}
	
	public function __construct($id, $from = NULL, $to = NULL){
		$this->__construct($id);
		$this->from = $from;
		$this->to = $to;
	}
	
//getter setter
	
	public function getId(){
		return $this->id;
	}
	
	public function setEdge($from, $to){
		$this->from = $from;
		$this->to = $to;
	}
	
//Encapsulated algorithem

}

?>