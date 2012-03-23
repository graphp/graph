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
			echo "\n Adding vertice $i,";
			
			$graph->addVertice(new Vertice($i));

			for ($k=0;$k<15;$k++)
			{
				// Add edges
				echo " edge $edgeCounter, ";
				$graph->addEdgeUndirected($edgeCounter);
				$edgeCounter++;
			}
		}

	}
}

?>
