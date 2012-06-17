<?php

use Fhaculty\Graph\Algorithm\MaxFlow\EdmondsKarp as AlgorithmMaxFlowEdmondsKarp;
use Fhaculty\Graph\Loader\EdgeListWithCapacity as LoaderEdgeListWithCapacity;

class MaxFlowEdmondsKarpTest extends PHPUnit_Framework_TestCase
{
    private function readGraph($file){
        $LoaderEdgeListWithCapacity = new LoaderEdgeListWithCapacity(PATH_DATA.$file);
        $LoaderEdgeListWithCapacity->setEnableDirectedEdges(true);
        
        return $LoaderEdgeListWithCapacity->createGraph();
    }
    
    /**
     * run algorithm with small graph and check result against known result
     */
    public function testKnownResultSmall()
    {
        $graph = $this->readGraph('Fluss.txt');

        $alg = new AlgorithmMaxFlowEdmondsKarp($graph->getVertex(0),$graph->getVertex(7));
        
        $this->assertEquals(4, $alg->getFlowMax());
    }
    
    /**
     * run algorithm with bigger graph and check result against known result (will take several seconds)
     */
//     public function testKnownResultBig(){
        
//         $graph = $this->readGraph('G_1_2.txt');
        
//         $alg = new AlgorithmMaxFlowEdmondsKarp($graph->getVertex(0),$graph->getVertex(4));
        
//         $this->assertEquals(0.735802,$alg->getFlowMax());
//     }
    
}
