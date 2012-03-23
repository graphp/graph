<?php

include_once 'graph.php';
include_once 'vertice.php';

interface Loader{
	
	public function getGraphFromFile($fileName);
	
}

?>