<?php

abstract class Loader{
    
    private $debugMode = false;
    
	protected $directedEdges = false;
	
	public abstract function getGraph();
	
	public function setEnableDirectedEdges($directedEdges){
		$this->directedEdges = $directedEdges;
	}
	
	private function writeDebugMessage($messageString){
	    if($this->debugMode){
	        echo $messageString;
	    }
	
	}
}
