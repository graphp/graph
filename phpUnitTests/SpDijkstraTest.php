<?php

use Fhaculty\Graph\Algorithm\SpDijkstra as AlgorithmSpDijkstra;
use Fhaculty\Graph\Loader\EdgeListWeighted as LoaderEdgeListWeighted;

class MSpDijkstraTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    // Run this code without crashing
    public function testRunningAlgorithm()
    {
        $file = "Wege1.txt";

        $LoaderEdgeListWeighted = new LoaderEdgeListWeighted(PATH_DATA.$file);
        $LoaderEdgeListWeighted->setEnableDirectedEdges(true);

        $this->testedGraph = $LoaderEdgeListWeighted->createGraph();

        $alg = new AlgorithmSpDijkstra($this->testedGraph->getVertex(0));
        //$alg = new AlgorithmSpDijkstra( Vertex::getFirst($steffiGraf->getVertices(),Vertex::ORDER_RANDOM) );

        $newGraph =  $alg->createGraph();

        $targetVertex = $this->testedGraph->getVertex("5");

        $this->assertEquals(4, $alg->getDistance($targetVertex));
    }
}
