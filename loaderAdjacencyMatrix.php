<?php
include 'loader.php';
include 'graph.php';

class LoaderAdjacencyMatrix implements Loader{

	public function getGraphFromFile($fileName)
	{
		$graph = new Graph();

		$file = file($fileName);
		$verticeCount = $file[0];
		$edgeCounter = 0;

		for ($i=1;$i<=15;$i++){

			// Add Vertices
			echo "Adding vertice $i \n";
			$graph->addVertice($i);

			for ($k=0;$k<15;$k++)
			{
				// Add edges
				$graph->addEdgeUndirected($edgeCounter);
				$edgeCounter++;
			}
		}

	}
}

?>
