<?php

use Fhaculty\Graph\Algorithm\Degree as AlgorithmDegree;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;

class DegreeTest extends TestCase
{
    public function testGraphEmpty()
    {
        $graph = new Graph();

        $alg = new AlgorithmDegree($graph);

        try {
            $alg->getDegree();
            $this->fail();
        }
        catch (UnderflowException $e) { }

        try {
            $alg->getDegreeMin();
            $this->fail();
        }
        catch (UnderflowException $e) { }

        try {
            $alg->getDegreeMax();
            $this->fail();
        }
        catch (UnderflowException $e) { }

        $this->assertTrue($alg->isRegular());
        $this->assertTrue($alg->isBalanced());
    }

    public function testGraphIsolated()
    {
        $graph = new Graph();
        $graph->createVertex(1);
        $graph->createVertex(2);

        $alg = new AlgorithmDegree($graph);

        $this->assertEquals(0, $alg->getDegree());
        $this->assertEquals(0, $alg->getDegreeMin());
        $this->assertEquals(0, $alg->getDegreeMax());
        $this->assertTrue($alg->isRegular());
        $this->assertTrue($alg->isBalanced());
    }

    public function testGraphIrregular()
    {
        // 1 -> 2 -> 3
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));
        $graph->getVertex(2)->createEdgeTo($graph->createVertex(3));

        $alg = new AlgorithmDegree($graph);

        try {
            $this->assertEquals(0, $alg->getDegree());
            $this->fail();
        }
        catch (UnexpectedValueException $e) { }

        $this->assertEquals(1, $alg->getDegreeMin());
        $this->assertEquals(2, $alg->getDegreeMax());
        $this->assertFalse($alg->isRegular());
        $this->assertFalse($alg->isBalanced());
    }
}
