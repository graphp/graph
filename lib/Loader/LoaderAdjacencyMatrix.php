<?php

class LoaderAdjacencyMatrix extends LoaderFile{
	
	private $debugMode = false;
	
	private $fileName;
	
	public function __construct($filename){
	    $this->fileName = $filename;
	}
	
	private function writeDebugMessage($messageString){
		if($this->debugMode){
			echo $messageString;
		}
		
	}
	
	public function getGraph()	{

		$graph = new Graph();

		$file = file($this->fileName, FILE_IGNORE_NEW_LINES);
		$vertexCount = $file[0];
		$edgeCounter = 0;
		
		$graph->createVertices($vertexCount);

		for ($i=0;$i<$vertexCount;$i++){

			// Add Vertices
			$this->writeDebugMessage("Adding vertex $i,");

			$thisVertex = $graph->getVertex($i);
			
			$currentEdgeList = explode("\t", $file[$i+1]);

			for ($k=0;$k<$vertexCount;$k++){
				
				// Add edges
				if($currentEdgeList[$k] != 0){
						
					$this->writeDebugMessage(" and edge #$edgeCounter: $i -> $k ");
					
					$thisVertex->createEdge($graph->getVertex($k));							//should this not be a directedEdge???
					
					$edgeCounter++;
				}
			
			}
			$this->writeDebugMessage("\n");
		}
		return $graph;
	}
}
