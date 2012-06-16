<?php

use Fhaculty\Graph\Algorithm\MCFSuccessiveShortestPath as AlgorithmMCFSuccessiveShortestPath;
use Fhaculty\Graph\Loader\EdgeListWithWeightedCapacityAndBalance as LoaderEdgeListWithWeightedCapacityAndBalance;

class MCFSuccessiveShortestPathTest extends PHPUnit_Framework_TestCase
{
    private $testedGraph = null;

    // Run this code without crashing
    public function testRunningAlgorithm()
    {
        $file = "Kostenminimal5.txt";

        $loader = new LoaderEdgeListWithWeightedCapacityAndBalance(PATH_DATA.$file);
        $loader->setEnableDirectedEdges(true);

        $steffiGraf = $loader->createGraph();

        $alg = new AlgorithmMCFSuccessiveShortestPath($steffiGraf);
        $newGraph =  $alg->createGraph();

        $this->assertEquals(-12, $alg->getWeightFlow());
    }
}
