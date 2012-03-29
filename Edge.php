<?php

abstract class Edge{
	/**
	 * returns the id of this Edge
	 * 
	 * @return int
	 * @todo purpose? REMOVE ME?	=> Tobias: don't remove, ID could be used as a name (for a path its maybe good to know witch Edge it takes)
	 */
	public function getId(){
		return 0;
	}
	
	abstract public function hasVertexTo($vertex);
	
	abstract public function hasVertexFrom($vertex);
	
	abstract public function getVerticesTo();
	
	abstract public function getVerticesFrom();
}
