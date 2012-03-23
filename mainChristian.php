<?php
include_once 'loaderAdjacencyMatrix.php';

$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();

$derGraf = $loaderAdjacencyMatrix->getGraphFromFile("data/Graph1.txt");

//foreach ($derGraf->getVertices() as $value) {
//	echo $value->getId()." ";
//}

$derGraf->searchDepthFirst(1);

// foreach ($derGraf->getEdgeArray() as $key => $value) {
// 	echo $key;
// 	print_r($value);
// } 
?>