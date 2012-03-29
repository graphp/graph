<?php

abstract class Edge{
	/**
	 * returns the id of this Edge
	 * 
	 * @return int
	 * @todo purpose? REMOVE ME?	=> Tobias: should be abstract
	 */
	public function getId(){
		return 0;
	}
	
	abstract public function hasVertexTo($vertex);
	
	abstract public function hasVertexFrom($vertex);
	
	abstract public function getVerticesTo();
	
	abstract public function getVerticesFrom();
}
