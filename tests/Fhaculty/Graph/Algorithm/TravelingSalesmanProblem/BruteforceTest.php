<?php

use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\Bruteforce;
use Fhaculty\Graph\Exception\UnexpectedValueException;

class BruteforceTest extends BaseTravelingSalesmanProblemTest
{
    protected function createAlg()
    {
        return new Bruteforce();
    }

    public function testUpperLimitHugeHasNoEffect()
    {
        $graph = $this->createGraphComplete(3, 2);
        $alg = $this->createAlg();

        $alg->setUpperLimit(100);

        $result = $alg->createResult($graph->getVertex(1));

        $this->assertEquals(6, $result->getWeight());
    }

    public function testUpperLimitExactlyHasNoEffect()
    {
        $graph = $this->createGraphComplete(3, 2);
        $alg = $this->createAlg();

        $alg->setUpperLimit(6);

        $result = $alg->createResult($graph->getVertex(1));

        $this->assertEquals(6, $result->getWeight());
    }

    public function testUpperLimitFromMinimumSpanningTree()
    {
        $graph = $this->createGraphComplete(3, 2);
        $alg = $this->createAlg();

        $alg->setUpperLimitMst($graph);

        $result = $alg->createResult($graph->getVertex(1));

        $this->assertEquals(6, $result->getWeight());
    }

    /**
     * @expectedException UnderflowException
     */
    public function testUpperLimitCanBeTooLow()
    {
        $graph = $this->createGraphComplete(3, 2);
        $alg = $this->createAlg();

        $alg->setUpperLimit(2);

        $alg->createResult($graph->getVertex(1));
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testNegativeWeightsFail()
    {
        $graph = $this->createGraphComplete(3, -1);
        $alg = $this->createAlg();

        $alg->createResult($graph->getVertex(1));
    }

    public function testNegativeWeightsWorkIfBranchAndBoundIsTurnedOff()
    {
        $graph = $this->createGraphComplete(3, -2);
        $alg = $this->createAlg();

        $alg->setBranchAndBound(false);
        $alg->setUpperLimit(2);

        $this->assertEquals(-6, $alg->createResult($graph->getVertex(1))->getWeight());
    }
}
