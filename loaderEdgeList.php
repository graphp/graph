<?php

include 'loader.php';
class LoaderEdgeList implements Loader{
	public function __construct(){
		echo "Du bist im Konstruktor von LoaderEdgeList\n";
		
		//echo file_get_contents ("data/Graph2.txt" );
		$zeilen = file ("data/Graph2.txt");
	
		foreach ($zeilen as $zeile) {
			if($zeile!=$zeilen[0]){			
			echo "hallo ";
			$array = explode("\t",$zeile);
			
			//echo "beides ";
			//echo $array;
			echo "zeile ";
			echo  $zeile;
			echo "zahl 0 ";
			echo $array[0];
			echo "\nzahl 1 ";
			echo $array[1];
			}
		}
		
	}
	public function getGraphFromFile($fileName){
		
	}	
}
$interface = new LoaderEdgeList();

?>