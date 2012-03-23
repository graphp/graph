<?php
include 'loaderAdjacencyMatrix.php';

$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();

$loaderAdjacencyMatrix->getGraphFromFile("data/Graph1.txt");

?>