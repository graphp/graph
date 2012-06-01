<?php

class LoaderAdjacencyMatrix extends LoaderFile{
	public function createGraph()	{

		$graph = new Graph();

		$file = $this->getLines();
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
