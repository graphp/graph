<?php

use Fhaculty\Graph\Algorithm\Balance as BalanceAlgorithm;
use Fhaculty\Graph\Graph;

class BalanceTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new BalanceAlgorithm($graph);

        $this->assertEquals(0, $alg->getBalance());
        $this->assertTrue($alg->isBalancedFlow());
    }

    public function testGraphSimple()
    {
        // source(+100) -> sink(-10)
        $graph = new Graph();
        $graph->createVertex('source')->setBalance(100);
        $graph->createVertex('sink')->setBalance(-10);

        $alg = new BalanceAlgorithm($graph);

        $this->assertEquals(90, $alg->getBalance());
        $this->assertFalse($alg->isBalancedFlow());
    }
}
