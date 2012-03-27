<?php

spl_autoload_register(function($class){
    require_once(__DIR__.'/'.str_replace('_','/',$class).'.php');
});

$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();

$derGraf = $loaderAdjacencyMatrix->getGraphFromFile("data/Graph1.txt");

//foreach ($derGraf->getVertices() as $value) {
//	echo $value->getId()." ";
//}

var_dump($derGraf->searchDepthFirst(1));

foreach ($derGraf->getEdgeArray() as $key => $value) {
	echo $key;
	print_r($value);
}

echo 'breitensuche ab 1:
';
$alg = new AlgorithmBreadthFirst($derGraf->getVertex(1));
var_dump($alg->getVerticesIds());
