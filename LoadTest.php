<?php

spl_autoload_register(function($class){
	require_once(__DIR__.'/'.str_replace('_','/',$class).'.php');
});

$graph = new Graph();
$n = 1000;

echo date(DATE_RFC822)." start creating vertices\n";
$graph->createVertices($n);

echo date(DATE_RFC822)." start creating edges\n";
for ($i = 2; $i < $n; ++$i){
       
       $vertex = $graph->getVertex($i);
       
       echo date(DATE_RFC822)." Vertex: $i\n";
       
       for ($j = $i; $j < $n; ++$j){
               $vertex->createEdge( $graph->getVertex($j) );
       }
}
echo "\n".date(DATE_RFC822)." start depth search\n\n";
$a = $graph->getVertex(4)->searchDepthFirst();
echo date(DATE_RFC822)." done .. \n\n";

echo date(DATE_RFC822)." start depth search\n\n";
$a = $graph->getVertex(4)->searchDepthFirst();
echo date(DATE_RFC822)." done .. \n\n";

echo date(DATE_RFC822)." start breath search\n\n";
$a = $graph->getVertex(4)->searchBreadthFirst();
echo date(DATE_RFC822)." done .. \n\n";

echo date(DATE_RFC822)." get # components\n\n";
$a = $graph->getNumberOfComponents();
echo date(DATE_RFC822)." done .. \n\n";

echo date(DATE_RFC822)." get Eulerian cycle\n\n";
$a = $graph->hasEulerianCycle();
echo date(DATE_RFC822)." done .. \n\n";
