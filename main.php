<?php

spl_autoload_register(function($class){
    require_once(__DIR__.'/'.str_replace('_','/',$class).'.php');
});

$interface = new main();

class main{
	
	private $graph = NULL;
	
	public function __construct(){
		echo "Read Graph-File:\n\n";
		
		$this->readFile();
		
		echo "\n";
		
		while(true){
			$this->chooseAction();
			
			echo "\n";
		}
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
			echo "1 = Vertex list\n";
		
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

	private function chooseAction(){
		$chooseOption = false;
		
		while($chooseOption == false)
		{
			echo "What do you want to do?\n";
			echo "0 = breadth-first search\n";
			echo "1 = depth-first search\n";
		
			fscanf(STDIN, "%d\n", $option);
		
			switch ($option){
				case 0:
					$this->startSearchBreadthFirst();
					$chooseOption = true;
					break;
				case 1:
					$this->startSearchDepthFirst();
					$chooseOption = true;
					break;
			}
		}
	}
	
	private function startSearchBreadthFirst(){
		echo "Please enter the name of the starting node?\n";
		
		fscanf(STDIN, "%s\n", $startingNode);
		
		print_r($this->graph->searchBreadthFirst($startingNode));
	}
	
	private function startSearchDepthFirst(){
		echo "Please enter the name of the starting node?\n";
	
		fscanf(STDIN, "%s\n", $startingNode);
	
		print_r($this->graph->searchDepthFirst($startingNode));
	}
}
