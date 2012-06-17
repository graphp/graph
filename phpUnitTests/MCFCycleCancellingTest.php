<?php

use Fhaculty\Graph\Algorithm\MinimumCostFlow\CycleCanceling as AlgorithmMCFCycleCanceling;
use Fhaculty\Graph\Loader\EdgeListWithWeightedCapacityAndBalance as LoaderEdgeListWithWeightedCapacityAndBalance;

class MCFCycleCancellingTest extends PHPUnit_Framework_TestCase
{
    public function testKnown4(){
        $this->assertEquals(3, $this->getResultFor('Kostenminimal4.txt'));
    }
    
    public function testKnown5(){
        $this->assertEquals(-12, $this->getResultFor('Kostenminimal5.txt'));
    }
    
    /**
     * run algorithm with bigger graph and check result against known result (will take several seconds)
     */
    public function testKnown100(){
    	$this->assertEquals(1537, $this->getResultFor('Kostenminimal100.txt'));
    }
    
    protected function getAlgFor($file){
        return new AlgorithmMCFCycleCanceling($this->getGraphFor($file));
    }
    
    protected function getGraphFor($file){
        $loader = new LoaderEdgeListWithWeightedCapacityAndBalance(PATH_DATA.$file);
        $loader->setEnableDirectedEdges(true);
        
        return $loader->createGraph();
    }
    
    protected function getResultFor($file){
        return $this->getAlgFor($file)->getWeightFlow();
    }
}
