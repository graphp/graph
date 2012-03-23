<?php

include_once 'loader.php';

class LoaderEdgeList implements Loader{
	public function __construct(){
		echo "constuktor LoaderEdgeList\n";
		
		
	}
	public function getGraphFromFile($fileName){
		//echo file_get_contents ("data/Graph2.txt" );
		$graph = new Graph();
		$file = file($fileName);
		$verticeCount = $file[0];
		
		
		// Add all vertices to the graph
		$vertexArray=array();
		for($i=0;$i<$verticeCount;$i++){
			$vertexArray[$i]=new Vertice($i);	
		}
		
		
		// Add all edge informations from the file
		$edgeCounter = 0;
		foreach ($file as $zeile) {
			//skip first entry, it contains only the number of vertices
			if($zeile==$file[0]){
				echo "Starting with {$zeile} elements\n";
			}
			else{
				$array = explode("\t",$zeile);
				$edge = new EdgeUndirected($edgeCounter,$array[0],$array[1]);
				// add edge to graph
				$graph->addEdge($edge);
				// add edge to vertice
				$vertexArray[$array[0]]->addEdge($edge);
				echo "added edge nr {$edgeCounter} from {$array[0]} to {$array[1]}\n";
				
				$edgeCounter++;
			}
		}
		for($i=0;$i<$verticeCount;$i++){
			$graph->addVertice(new Vertice($i));
		}
		echo "Added {$verticeCount} vertizes and {$edgeCounter} Nodes\n";
		
		return $graph;
		
	}	
}
$interface = new LoaderEdgeList();
$interface->getGraphFromFile("data/Graph2.txt");

?>