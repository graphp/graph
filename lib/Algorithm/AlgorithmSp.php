<?php

abstract class AlgorithmSp extends Algorithm {
    protected $startVertex;
    
    public function __construct(Vertex $startVertex){
    	$this->startVertex = $startVertex;
    }
}
