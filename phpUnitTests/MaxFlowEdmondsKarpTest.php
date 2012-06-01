<?php

require_once 'example/init.inc.php';

class MaxFlowEdmondsKarpTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    public function testLoadingRelatedGraph()
    {
        $file = "Fluss.txt";

        // Run this code without crashing
        $LoaderEdgeListWithCapacity = new LoaderEdgeListWithCapacity(PATH_DATA.$file);
        $LoaderEdgeListWithCapacity->setEnableDirectedEdges(true);

        $this->testedGraph = $LoaderEdgeListWithCapacity->getGraph();


        $alg = new AlgorithmMaxFlowEdmondsKarp($this->testedGraph->getVertex(0),$this->testedGraph->getVertex(7));
        $newGraph =  $alg->getResultGraph();

        $this->assertEquals(0, $alg->getMaxFlowValue());
    }
}
