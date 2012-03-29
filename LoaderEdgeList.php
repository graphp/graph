<?php

class LoaderEdgeList implements Loader{
	
	private $debugMode = false;
	
	private function writeDebugMessage($messageString){
		if($this->debugMode){
			echo $messageString;
		}
	}
	
	public function __construct(){
	}
	
	public function getGraphFromFile($fileName){
		
		$graph = new Graph();
		
		$file = file($fileName, FILE_IGNORE_NEW_LINES);
		$vertexCount = $file[0];
		$edgeCounter = 0;
		
		$graph->createVertices($vertexCount);
		
		unset($file[0]);
		foreach ($file as $zeile) {
			$edgeConections = explode("\t", $zeile);
			
			$from = $graph->getVertex($edgeConections[0]);
			$to = $graph->getVertex($edgeConections[1]);
			
			$from->createEdge($to);								//TODO directed
		}
		
		return $graph;
		
	}	
}
