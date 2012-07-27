<?php
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Prim as AlgorithmMSTPrim;
use Fhaculty\Graph\Loader\CompleteGraph as LoaderCompleteGraph;
use Fhaculty\Graph\Loader\EdgeListWeighted as LoaderEdgeListWeighted;

class MSTPrinTest extends PHPUnit_Framework_TestCase
{
    public function testKnownComplete(){
        $this->assertCount(4, $this->getResultForComplete(5));
    }
    
    public function testKnownHoever1() {
        $this->assertEquals(286.711151723, $this->getResultFor("G_1_2.txt"));
    }
    
    public function testKnownHoever2() {
        $this->assertEquals(29.549305006, $this->getResultFor("G_1_20.txt"));
    }
    
//     public function testKnownHoever3() {
//         $this->assertEquals(2775.4412042395, $this->getResultFor("G_10_20.txt"));
//     }
    
//     public function testKnownHoever4() {
//         $this->assertEquals(3.0227974635, $this->getResultFor("G_1_200.txt"));
//     }
    
//     public function testKnownHoever5() {
//         $this->assertEquals(301.551901033, $this->getResultFor("G_10_200.txt"));
//     }
    
//     public function testKnownHoever6() {
//         $this->assertEquals(27450.617104929, $this->getResultFor("G_100_200.txt"));
//     }
    
    /**
     * run algorithm with bigger graph and check result against known result (will take several seconds)
     */
    
    protected function getAlgFor($file){
        return new AlgorithmMSTPrim($this->getGraphFor($file)->getVertexFirst());
    }
    
    protected function getGraphFor($file){
        $loader = new LoaderEdgeListWeighted(PATH_DATA.$file);
        $loader->setEnableDirectedEdges(false);
        
        return $loader->createGraph();
    }
    
    protected function getResultFor($file){
        return $this->getAlgFor($file)->createGraph()->getWeight();
    }
    
    protected function getResultForComplete($n){
        $loader = new LoaderCompleteGraph($n);
        $loader->setEnableDirectedEdges(true);
        $alg = new AlgorithmMSTPrim($loader->createGraph()->getVertex(1));
        return $alg->getEdges();
    }
}
