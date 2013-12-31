<?php

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Loader\CompleteGraph;
use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\Result;

abstract class BaseTravelingSalesmanProblemTest extends TestCase
{
    abstract protected function createAlg();

    /**
     *
     * @param Vertex $vertex
     * @return Result
     */
    protected function createResult(Vertex $vertex)
    {
        return $this->createAlg()->createResult($vertex);
    }

    public function testCompleteFive()
    {
        $complete = new CompleteGraph(5);
        $graph = $complete->createGraph();

        foreach ($graph->getEdges() as $edge) {
            $edge->setWeight(2);
        }

        $v1 = $graph->getVertex(1);

        $result = $this->createResult($v1);

        $this->assertEquals(10, $result->getWeight());

        $rgraph = $result->createGraph();
        $this->assertNotSame($graph, $rgraph);

        $cycle = $result->getCycle();

        $this->assertCount(5, $cycle->getEdges());
        $this->assertCount(6, $cycle->getVertices());
        $this->assertSame($graph, $cycle->getGraph());

        $this->assertGraphEquals($rgraph, $cycle->createGraph());
    }

    /**
     * @expectedException UnderflowException
     */
    public function testSingleVertexDoesNotFormCycle()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex(1);

        $this->createResult($v1);
    }

    /**
     * @expectedException UnderflowException
     */
    public function testLineDoesNotFormCycle()
    {
        // 1 -- 2 -- 3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $v1->createEdge($v2);
        $v2->createEdge($v3);

        $this->createResult($v1);
    }

    /**
     * @expectedException UnderflowException
     */
    public function testMultipleComponentsDoNotFormCycle()
    {
        // 1 -- 2 , 3 -- 4
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);
        $v4 = $graph->createVertex(4);
        $v1->createEdge($v2);
        $v3->createEdge($v4);

        $this->createResult($v1);
    }
}
