<?php
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Prim as AlgorithmMSTPrim;
use Fhaculty\Graph\Loader\CompleteGraph as LoaderCompleteGraph;

class PrimTest extends PHPUnit_Framework_TestCase
{
    public function testKnownComplete()
    {
        $this->assertCount(4, $this->getResultForComplete(5));
    }

    protected function getResultForComplete($n)
    {
        $loader = new LoaderCompleteGraph($n);
        $loader->setEnableDirectedEdges(false);
        $alg = new AlgorithmMSTPrim($loader->createGraph()->getVertex(1));

        return $alg->getEdges();
    }
}
