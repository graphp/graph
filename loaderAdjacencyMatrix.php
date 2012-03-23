<?php
include 'loader.php';
include 'graph.php';

class LoaderAdjacencyMatrix implements Loader{

	public function getGraphFromFile($fileName)
	{
		$hubba = new Graph();

		$file = file($fileName);

		$verticeCount = $file[0];
			
		for ($i=1;$i<=15;$i++)
			for ($k=0;$k<15;$k++)
			{
				echo 'add' + $k;
				$hubba->addEdge($k);
			}
	}

}

?>