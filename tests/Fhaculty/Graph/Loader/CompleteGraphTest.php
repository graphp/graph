<?php

use Fhaculty\Graph\Loader\CompleteGraph;

class CompleteGraphTest extends PHPUnit_Framework_TestCase
{
    public function testOne(){
        $loader = new CompleteGraph(1);
        $graph = $loader->createGraph();
        
        $this->assertEquals(1,$graph->getNumberOfVertices());
        $this->assertEquals(0,$graph->getNumberOfEdges());
        $this->assertFalse($graph->isDirected());
    }
    
    public function testTen(){
        $loader = new CompleteGraph(10);
        $graph = $loader->createGraph();
    
        $this->assertEquals(10,$graph->getNumberOfVertices()); // $n
        $this->assertEquals(45,$graph->getNumberOfEdges()); // n*(n-1)/2
    }
    
    public function testDirected(){
        $loader = new CompleteGraph(5);
        $loader->setEnableDirectedEdges(true);
        $graph = $loader->createGraph();
    
        $this->assertEquals(5,$graph->getNumberOfVertices());
        $this->assertEquals(20,$graph->getNumberOfEdges()); // n*(n-1) for directed graphs
        $this->assertTrue($graph->isDirected());
        $this->assertTrue($graph->isComplete());
    }
}
