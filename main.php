<?php

include_once 'loaderEdgeList.php';
include_once 'loaderAdjacencyMatrix.php';
include_once 'graph.php';

$interface = new main();

class main{
	
	private $graph = NULL;
	
	public function __construct(){
		echo "Read Graph-File:\n";
		
		$this->readFile();
	}
	
	private function readFile(){
		$fileFormatRightInput = false;
		$fileNameRightInput = false;
		
		$fileFormat = NULL;
		$fileName = NULL;
		
		while($fileFormatRightInput == false)
		{
			echo "What kind of File you want to read?\n";
			echo "0 = Adjacency matrix\n";
			echo "1 = Vertice list\n";
		
			fscanf(STDIN, "%d\n", $fileFormat);
		
			switch ($fileFormat){
				case 0:
				case 1:
					$fileFormatRightInput = true;
			}
		}
		
		while($fileNameRightInput == false)
		{
			echo "Please enter the file name?\n";
		
			fscanf(STDIN, "%s\n", $fileName);
			
			$fileNameRightInput = true;
		}
		
		switch ($fileFormat){
			case 0:
				$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();
				$this->graph = $loaderAdjacencyMatrix->getGraphFromFile($fileName);
				$rightInput = true;
				break;
			case 1:
				$interface = new LoaderEdgeList();
				$this->graph = $interface->getGraphFromFile($fileName);
				$rightInput = true;
				break;
		}
	}
}

?>