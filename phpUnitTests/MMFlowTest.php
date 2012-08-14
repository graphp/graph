<?php

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Algorithm\MaximumMatching\Flow;

use Fhaculty\Graph\Loader\EdgeListBipartit;

class MMFlowTest extends PHPUnit_Framework_TestCase
{    
    /**
     * run algorithm with small graph and check result against known result
     */
    public function testKnownResult()
    {
        $loader = new EdgeListBipartit(PATH_DATA.'Matching_100_100.txt');
        $loader->setEnableDirectedEdges(false);
        $graph = $loader->createGraph();
        
        $alg = new Flow($graph);
        $this->assertEquals(100, $alg->getNumberOfMatches());
    }

    public function testSimple(){
    	$graph = new Graph();
    	$graph->createVertex(0)->setGroup(0)->createEdge($graph->createVertex(1)->setGroup(1));
    
    	$alg = new Flow($graph);
    	$this->assertEquals(1,$alg->getNumberOfMatches());
    }
    
    /**
     * expect exception for directed edges
     * @expectedException UnexpectedValueException
     */
    public function testInvalidDirected(){
        $graph = new Graph();
        $graph->createVertex(0)->setGroup(0)->createEdgeTo($graph->createVertex(1)->setGroup(1));
        
        $alg = new Flow($graph);
        $alg->getNumberOfMatches();
    }
    
    /**
     * expect exception for non-bipartit graphs
     * @expectedException UnexpectedValueException
     */
    public function testInvalidBipartit(){
    	$graph = new Graph();
    	$graph->createVertex(0)->setGroup(1)->createEdge($graph->createVertex(1)->setGroup(1));
    
    	$alg = new Flow($graph);
    	$alg->getNumberOfMatches();
    }
}
