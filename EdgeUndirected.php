<?php

class EdgeUndirected extends Edge{
	private $a;
	private $b;
	
	public function __construct($a,$b){
	    $this->a = $a;
	    $this->b = $b;
	}
	
    public function getVerticesFrom(){
        return array($this->a,$this->b);
    }
    
    public function getVerticesTo(){
        return array($this->a,$this->b);
    }
    
    public function hasVertexFrom($vertex){
        return ($this->a === $vertex || $this->b === $vertex);
    }
    
    public function hasVertexTo($vertex){
        return ($this->a === $vertex || $this->b === $vertex);
    }
}
