<?php

require_once 'example/init.inc.php';

class MSpDijkstraTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    // Run this code without crashing
    public function testRunningAlgorithm()
    {
        $file = "Wege3.txt";

        $LoaderEdgeListWeighted = new LoaderEdgeListWeighted(PATH_DATA.$file);
        $LoaderEdgeListWeighted->setEnableDirectedEdges(true);

        $this->testedGraph = $LoaderEdgeListWeighted->createGraph();

        $alg = new AlgorithmSpDijkstra($this->testedGraph->getVertex(0));
        //$alg = new AlgorithmSpDijkstra( Vertex::getFirst($steffiGraf->getVertices(),Vertex::ORDER_RANDOM) );

        $newGraph =  $alg->createGraph();
    }
}
