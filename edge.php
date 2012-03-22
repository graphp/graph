<?php

class Edge{
	private $id = NULL;
	private $from = NULL;
	private $to = NULL;
	
	public function __construct(){ }

	public function __construct($id){
		$this->id = $id;
	}
	
	public function __construct($id, $from, $to){
		$this->__construct($id);
		$this->from = $from;
		$this->to = $to;
	}
	
//Encapsulated algorithem

	//Breadth-first search (prototyp)
	public function searchBreadthFirst(){
		$alg = new BreitenSuche_Agl($this);
		return $alg->getResult();
	}
}

?>