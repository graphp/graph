<?php

require_once 'example/init.inc.php';

class MCFCycleCancellingTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    // Run this code without crashing
    public function testRunningAlgorithm()
    {
        $file = "Kostenminimal5.txt";

        $loader = new LoaderEdgeListWithWeightedCapacityAndBalance(PATH_DATA.$file);
        $loader->setEnableDirectedEdges(true);

        $steffiGraf = $loader->createGraph();

        $alg = new AlgorithmMCFCycleCanceling($steffiGraf);
        $newGraph =  $alg->createGraph();

        $this->assertEquals(-12, $alg->getWeightFlow());
    }
}
