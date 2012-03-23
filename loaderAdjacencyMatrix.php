<?php
include 'loader.php';

class LoaderAdjacencyMatrix implements Loader{

	public function getGraphFromFile($fileName){
		$zeilen = file ($fileName);

		foreach ($zeilen as $zeile) {
			echo $zeile;
		}
	}
	
}

?>