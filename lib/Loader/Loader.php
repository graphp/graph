<?php

abstract class Loader{
	
	protected $directedEdges = false;
	
	public abstract function getGraph();
	
	public function setEnableDirectedEdges($directedEdges){
		$this->directedEdges = $directedEdges;
	}
	
}
