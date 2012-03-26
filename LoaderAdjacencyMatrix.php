<?php

class LoaderAdjacencyMatrix implements Loader{
	
	private $debugMode = false;
	
	private function writeDebugMessage($messageString){
		if($this->debugMode){
			echo $messageString;
		}
		
	}
	
	public function getGraphFromFile($fileName)	{

		$graph = new Graph();

		$file = file($fileName);
		$vertexCount = $file[0];
		$edgeCounter = 0;

		for ($i=0;$i<$vertexCount;$i++){

			// Add Vertices
			$this->writeDebugMessage("Adding vertex $i,");

			$thisVertex = new Vertex($i);
			$graph->addVertex($thisVertex);
			
			$currentEdgeList = explode("\t", $file[$i+1]);

			for ($k=0;$k<$vertexCount;$k++){
				
				// Add edges
				if($currentEdgeList[$k] != 0){
						
					$this->writeDebugMessage(" and edge #$edgeCounter: $i -> $k ");
					
					$currentEdge = new EdgeUndirected($edgeCounter);
					$currentEdge->setEdgeIds($i, $k);
						
					$thisVertex->addEdgeId($currentEdge->getId());
					$graph->addEdge($currentEdge);

					$edgeCounter++;
				}
			
			}
			$this->writeDebugMessage("\n");
		}
		return $graph;
	}
}
