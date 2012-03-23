<?php
include_once 'loaderAdjacencyMatrix.php';
include_once 'vertice.php';

$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();

$derGraf = $loaderAdjacencyMatrix->getGraphFromFile("data/Graph1.txt");

echo "\n";
//foreach ($derGraf->getVertices() as $value) {
//	echo $value->getId()." ";
//}

$derGraf->searchDepthFirst(1);

?>