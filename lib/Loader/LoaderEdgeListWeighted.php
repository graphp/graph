<?php

class LoaderEdgeListWeighted extends LoaderFile{
	
	private $debugMode = false;
	
	private $fileName;
	
	private function writeDebugMessage($messageString){
		if($this->debugMode){
			echo $messageString;
		}
	}
	
	public function __construct($filename){
	    $this->fileName = $filename;
	}
	
	public function getGraph(){
		
		$graph = new Graph();
		
		$file = file($this->fileName, FILE_IGNORE_NEW_LINES);
		$vertexCount = $file[0];
		$edgeCounter = 0;
		
		$graph->createVertices($vertexCount);
		
		unset($file[0]);
		foreach ($file as $zeile) {
			$edgeConnections = explode("\t", $zeile);
			
			$from = $graph->getVertex($edgeConnections[0]);
			$to = $graph->getVertex($edgeConnections[1]);
			
			$edge = $from->createEdge($to);                                     //TODO directed
			$edge->setWeight((float)$edgeConnections[2]);
		}
		
		return $graph;
		
	}	
}
