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
		$verticeCount = $file[0];
		$edgeCounter = 0;

		for ($i=0;$i<$verticeCount;$i++){

			// Add Vertices
			$this->writeDebugMessage("Adding vertice $i,");

			$thisVertice = new Vertice($i);
			$graph->addVertice($thisVertice);
			
			$currentEdgeList = explode("\t", $file[$i+1]);

			for ($k=0;$k<$verticeCount;$k++){
				
				// Add edges
				if($currentEdgeList[$k] != 0){
						
					$this->writeDebugMessage(" and edge #$edgeCounter: $i -> $k ");
					
					$currentEdge = new EdgeUndirected($edgeCounter);
					$currentEdge->setEdgeIds($i, $k);
						
					$thisVertice->addEdgeId($currentEdge->getId());
					$graph->addEdge($currentEdge);

					$edgeCounter++;
				}
			
			}
			$this->writeDebugMessage("\n");
		}
		return $graph;
	}
}
