<?php

use Fhaculty\Graph\Exception\RuntimeException;
use Fhaculty\Graph\Exporter\Image;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\OverflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Graph;

class GraphTest extends TestCase
{
    public function setup()
    {
        $this->graph = new Graph();
    }

    public function testVertexClone()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(123)->setBalance(10)->setGroup(4);

        $newgraph = new Graph();
        $newvertex = $newgraph->createVertexClone($vertex);

        $this->assertVertexEquals($vertex, $newvertex);
    }

    /**
     * test to make sure vertex can not be cloned into same graph (due to duplicate id)
     *
     * @expectedException RuntimeException
     */
    public function testInvalidVertexClone()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(123);
        $graph->createVertexClone($vertex);
    }

    public function testGraphCloneEmpty()
    {
        $graph = new Graph();
        $newgraph = $graph->createGraphClone();
        $this->assertGraphEquals($graph, $newgraph);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testGetVertexNonexistant()
    {
        $graph = new Graph();
        $graph->getVertex('non-existant');
    }

    public function testGraphClone()
    {
        $graph = new Graph();
        $graph->createVertex(123)->setBalance(10)->setGroup(4);

        $newgraph = $graph->createGraphClone();

        $this->assertGraphEquals($graph, $newgraph);

        $graphClonedTwice = $newgraph->createGraphClone();

        $this->assertGraphEquals($graph, $graphClonedTwice);
    }

    public function testGraphCloneEdgeless()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdgeTo($graph->createVertex(2));
        $graph->createVertex(3)->createEdge($graph->getVertex(2));

        $graphEdgeless = $graph->createGraphCloneEdgeless();

        $graphExpected = new Graph();
        $graphExpected->createVertex(1);
        $graphExpected->createVertex(2);
        $graphExpected->createVertex(3);

        $this->assertGraphEquals($graphExpected, $graphEdgeless);
    }

    /**
     * check to make sure we can actually create vertices with automatic IDs
     */
    public function testCanCreateVertex()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex();
        $this->assertInstanceOf('\Fhaculty\Graph\Vertex', $vertex);
    }

    /**
     * check to make sure we can actually create vertices with automatic IDs
     */
    public function testCanCreateVertexId()
    {
        $graph = new Graph();
        $vertex = $graph->createVertex(11);
        $this->assertInstanceOf('\Fhaculty\Graph\Vertex', $vertex);
        $this->assertEquals(11, $vertex->getId());
    }

    /**
     * fail to create two vertices with same ID
     * @expectedException OverflowException
     */
    public function testFailDuplicateVertex()
    {
        $graph = new Graph();
        $graph->createVertex(33);
        $graph->createVertex(33);
    }

    public function testExporter()
    {
        $graph = new Graph();
        $graph->createVertex(1)->createEdge($graph->createVertex(2));

        $this->assertNotEquals('', (string)$graph);

        $this->assertInstanceOf('\\Fhaculty\\Graph\\Exporter\\ExporterInterface', $graph->getExporter());
    }

    public function testHasVertex()
    {
        $graph = new Graph();
        $graph->createVertex(1);
        $graph->createVertex('string');

        // check integer IDs
        $this->assertFalse($graph->hasVertex(2));
        $this->assertTrue($graph->hasVertex(1));

        // check string IDs
        $this->assertFalse($graph->hasVertex('non-existant'));
        $this->assertTrue($graph->hasVertex('string'));

        // integer IDs can also be checked as string IDs
        $this->assertTrue($graph->hasVertex('1'));
    }

    public function testCreateMultigraph()
    {
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);

        $e1 = $v1->createEdge($v2);
        $e2 = $v1->createEdge($v2);

        $this->assertEquals(2, $graph->getNumberOfEdges());
        $this->assertEquals(2, count($v1->getEdges()));

        $this->assertEquals(array(2), $v1->getVerticesEdge()->getIds());
    }

    public function testCreateMixedGraph()
    {
        // v1 -- v2 -> v3
        $graph = new Graph();
        $v1 = $graph->createVertex(1);
        $v2 = $graph->createVertex(2);
        $v3 = $graph->createVertex(3);

        $v1->createEdge($v2);
        $v2->createEdgeTo($v3);

        $this->assertEquals(2, $graph->getNumberOfEdges());

        $this->assertEquals(2, count($v2->getEdges()));
        $this->assertEquals(2, count($v2->getEdgesOut()));
        $this->assertEquals(1, count($v2->getEdgesIn()));

        $this->assertEquals(array(1, 3), $v2->getVerticesEdgeTo()->getIds());
        $this->assertEquals(array(1), $v2->getVerticesEdgeFrom()->getIds());
    }

    public function testCreateVerticesNone()
    {
        $graph = new Graph();

        $this->assertEquals(array(), $graph->createVertices(0));
        $this->assertEquals(array(), $graph->createVertices(array()));

        $this->assertEquals(0, $graph->getNumberOfVertices());
    }

    /**
     * expect to fail for invalid number of vertices
     * @expectedException InvalidArgumentException
     * @dataProvider testCreateVerticesFailProvider
     */
    public function testCreateVerticesFail($number)
    {
        $graph = new Graph();
        $graph->createVertices($number);
    }

    public static function testCreateVerticesFailProvider()
    {
        return array(
            array(-1),
            array("10"),
            array(0.5),
            array(null),
            array(array(1, 1))
        );
    }

    public function testCreateVerticesOkay()
    {
        $graph = new Graph();

        $vertices = $graph->createVertices(2);
        $this->assertCount(2, $vertices);
        $this->assertEquals(array(0, 1), $graph->getVertices()->getIds());

        $vertices = $graph->createVertices(array(7, 9));
        $this->assertCount(2, $vertices);
        $this->assertEquals(array(0, 1, 7, 9), $graph->getVertices()->getIds());

        $vertices = $graph->createVertices(3);
        $this->assertCount(3, $vertices);
        $this->assertEquals(array(0, 1, 7, 9, 10, 11, 12), $graph->getVertices()->getIds());
    }

    public function testCreateVerticesAtomic()
    {
        $graph = new Graph();

        // create vertices 10-19 (inclusive)
        $vertices = $graph->createVertices(range(10, 19));
        $this->assertCount(10, $vertices);

        try {
            $graph->createVertices(array(9, 19, 20));
            $this->fail('Should be unable to create vertices because of duplicate IDs');
        }
        catch (OverflowException $ignoreExpected) {
            $this->assertEquals(10, $graph->getNumberOfVertices());
        }

        try {
            $graph->createVertices(array(20, 21, 21));
            $this->fail('Should be unable to create vertices because of duplicate IDs');
        }
        catch (InvalidArgumentException $ignoreExpected) {
            $this->assertEquals(10, $graph->getNumberOfVertices());
        }
    }
}
