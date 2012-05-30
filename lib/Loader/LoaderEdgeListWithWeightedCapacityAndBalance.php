<?php

class LoaderEdgeListWithWeightedCapacityAndBalance extends LoaderFile{
	
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
	
	/**
	 * 
	 */
	public function getGraph(){
		
		$graph = new Graph();
		
		$file = file($this->fileName, FILE_IGNORE_NEW_LINES);
		$vertexCount = $file[0];
		$edgeCounter = 0;
		
		$graph->createVertices($vertexCount);
		unset($file[0]);
		
		// set the value of the vertices
		$zeile=1;
		foreach ($graph->getVertices() as $vertex)
		{
		    $vertex->setValue($file[$zeile]);
		    unset($file[$zeile]);
		    ++$zeile;
		}
		
		
		foreach ($file as $zeile) {
			$edgeConnections = explode("\t", $zeile);
			
			$from = $graph->getVertex($edgeConnections[0]);
			$to = $graph->getVertex($edgeConnections[1]);
			
			$edge;
			
			if ($this->directedEdges){
				$edge = $from->createEdgeTo($to);
			}
			else {
				$edge = $from->createEdge($to);
			}
			
			$edge->setWeight((float)$edgeConnections[2]);
			$edge->setCapacity((float)$edgeConnections[3]);
		}
		
		return $graph;
		
	}	
}
