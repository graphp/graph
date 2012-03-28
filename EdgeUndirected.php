<?php

class EdgeUndirected extends Edge{
	private $a;
	private $b;
	
	public function __construct($a,$b){
	    $this->a = $a;
	    $this->b = $b;
	}
}
