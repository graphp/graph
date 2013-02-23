<?php

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Loader\CompleteGraph;

class CompleteGraphTest extends TestCase
{
    public function testOne()
    {
        $loader = new CompleteGraph(1);
        $graph = $loader->createGraph();

        $expected = new Graph();
        $expected->createVertex();

        $this->assertGraphEquals($expected,$graph);
    }

    public function testUndirected()
    {
        $n = 9;

        $loader = new CompleteGraph($n);
        $graph = $loader->createGraph();

        $this->assertEquals($n,$graph->getNumberOfVertices());
        $this->assertEquals($n*($n-1)/2,$graph->getNumberOfEdges());
    }

    public function testDirected()
    {
        $n = 8;

        $loader = new CompleteGraph($n);
        $loader->setEnableDirectedEdges(true);
        $graph = $loader->createGraph();

        $this->assertEquals($n,$graph->getNumberOfVertices());
        $this->assertEquals($n*($n-1),$graph->getNumberOfEdges()); // n*(n-1) for directed graphs
        $this->assertTrue($graph->isDirected());
        $this->assertTrue($graph->isComplete());
    }
}
