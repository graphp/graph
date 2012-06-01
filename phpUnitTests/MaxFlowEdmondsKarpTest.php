<?php

require_once 'example/init.inc.php';

class MaxFlowEdmondsKarpTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    // Run this code without crashing
    public function testRunningAlgorithm()
    {
        $file = "Fluss.txt";

        $LoaderEdgeListWithCapacity = new LoaderEdgeListWithCapacity(PATH_DATA.$file);
        $LoaderEdgeListWithCapacity->setEnableDirectedEdges(true);

        $this->testedGraph = $LoaderEdgeListWithCapacity->createGraph();

        $alg = new AlgorithmMaxFlowEdmondsKarp($this->testedGraph->getVertex(0),$this->testedGraph->getVertex(7));
        $newGraph =  $alg->createGraph();

        $this->assertEquals(4, $alg->getMaxFlowValue());
    }
}
