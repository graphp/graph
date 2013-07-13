<?php

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Algorithm\MaxFlow\EdmondsKarp as AlgorithmMaxFlowEdmondsKarp;

class EdmondsKarpTest extends PHPUnit_Framework_TestCase
{
    public function testEdgeDirected()
    {
        $graph = new Graph();
        $v0 = $graph->createVertex(0);
        $v1 = $graph->createVertex(1);

        $v0->createEdgeTo($v1)->setCapacity(10);

        $alg = new AlgorithmMaxFlowEdmondsKarp($v0, $v1);

        $this->assertEquals(10, $alg->getFlowMax());
    }

    public function testEdgesMultiplePaths()
    {
        $graph = new Graph();
        $v0 = $graph->createVertex(0);
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);

        $v0->createEdgeTo($v1)->setCapacity(5);
        $v0->createEdgeTo($v2)->setCapacity(7);
        $v2->createEdgeTo($v1)->setCapacity(9);

        $alg = new AlgorithmMaxFlowEdmondsKarp($v0, $v1);

        $this->assertEquals(12, $alg->getFlowMax());
    }

//     public function testEdgesParallel(){
//         $graph = new Graph();
//         $v0 = $graph->createVertex(0);
//         $v1 = $graph->createVertex(1);

//         $v0->createEdgeTo($v1)->setCapacity(3.4);
//         $v0->createEdgeTo($v1)->setCapacity(6.6);

//         $alg = new AlgorithmMaxFlowEdmondsKarp($v0, $v1);

//         $this->assertEquals(10, $alg->getFlowMax());
//     }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testEdgesUndirected()
    {
        $graph = new Graph();
        $v0 = $graph->createVertex(0);
        $v1 = $graph->createVertex(1);

        $v1->createEdge($v0)->setCapacity(7);

        $alg = new AlgorithmMaxFlowEdmondsKarp($v0, $v1);

        $this->assertEquals(7, $alg->getFlowMax());
    }

    /**
     * run algorithm with bigger graph and check result against known result (will take several seconds)
     */
//     public function testKnownResultBig(){

//         $graph = $this->readGraph('G_1_2.txt');

//         $alg = new AlgorithmMaxFlowEdmondsKarp($graph->getVertex(0), $graph->getVertex(4));

//         $this->assertEquals(0.735802, $alg->getFlowMax());
//     }


    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFlowToOtherGraph()
    {
        $graph1 = new Graph();
        $vg1 = $graph1->createVertex(1);

        $graph2 = new Graph();
        $vg2 = $graph2->createVertex(2);

        new AlgorithmMaxFlowEdmondsKarp($vg1, $vg2);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFlowToSelf()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex(1);

        new AlgorithmMaxFlowEdmondsKarp($v1, $v1);
    }

}
