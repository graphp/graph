<?php

use Fhaculty\Graph\Algorithm\Complete;

use Fhaculty\Graph\Algorithm\Directed;

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

        $this->assertGraphEquals($expected, $graph);
    }

    public function testUndirected()
    {
        $n = 9;

        $loader = new CompleteGraph($n);
        $graph = $loader->createGraph();

        $this->assertEquals($n, count($graph->getVertices()));
        $this->assertEquals($n*($n-1)/2, count($graph->getEdges()));
    }

    public function testDirected()
    {
        $n = 8;

        $loader = new CompleteGraph($n);
        $loader->setEnableDirectedEdges(true);
        $graph = $loader->createGraph();

        $this->assertEquals($n, count($graph->getVertices()));
        $this->assertEquals($n*($n-1), count($graph->getEdges())); // n*(n-1) for directed graphs

        $alg = new Directed($graph);
        $this->assertTrue($alg->hasDirected());

        $alg = new Complete($graph);
        $this->assertTrue($alg->isComplete());
    }
}
