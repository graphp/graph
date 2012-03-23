<?php
include 'loader.php';
include 'graph.php';
include 'vertice.php';

class LoaderAdjacencyMatrix implements Loader{

	public function getGraphFromFile($fileName)
	{
		$graph = new Graph();

		$file = file($fileName);
		$verticeCount = $file[0];
		$edgeCounter = 0;

		for ($i=1;$i<=15;$i++){

			// Add Vertices
			echo "Adding vertice $i,";
			
			$graph->addVertice(new Vertice($i));
			$currentEdgeList = explode("\t", $file[$i]);
			
			for ($k=0;$k<15;$k++)
			{
				// Add edges
				if($currentEdgeList[$k] != 0){
					echo " edge $k, ";
				}
				
				$graph->addEdgeUndirected($edgeCounter);
				$edgeCounter++;
			}
			echo "\n";
		}

	}
}

?>
