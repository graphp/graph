<?php

spl_autoload_register(function($class){
    require_once(__DIR__.'/'.str_replace('_','/',$class).'.php');
});

function grad($graph){
    echo 'grad: '.count($graph).'
';
}

$loaderAdjacencyMatrix = new LoaderAdjacencyMatrix();

$derGraf = $loaderAdjacencyMatrix->getGraphFromFile("data/Graph1.txt");

grad($derGraf);

//foreach ($derGraf->getVertices() as $value) {
//	echo $value->getId()." ";
//}

//var_dump($derGraf->searchDepthFirst(1));

// foreach ($derGraf->getEdges() as $key => $value) {
// 	echo $key;
// 	print_r($value);
// }

echo 'breitensuche ab 1:
';
$alg = new AlgorithmSearchBreadthFirst($derGraf->getVertex(1));
echo implode(' ',$alg->getVerticesIds());

$derGraf->createVertices(100000);

grad($derGraf);

$derGraf->createVertices(10000);

grad($derGraf);

$LoaderEdgeList = new LoaderEdgeList();

$steffiGraf = $LoaderEdgeList->getGraphFromFile("data/Graph2.txt");

$steffiGrafBild = new GraphViz($steffiGraf);

$script = $steffiGrafBild->createDirectedGraphVizScript();

$newfile="graph.gv";
$file = fopen ($newfile, "w");
fwrite($file, $script);
fclose ($file);

echo "Generate picture ...";
exec("dot -Tpng graph.gv -o graph.png && eog graph.png"); 
echo "... done\n";
//echo $script;