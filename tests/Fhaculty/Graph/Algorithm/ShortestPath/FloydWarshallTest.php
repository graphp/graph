<?php

use Fhaculty\Graph\Algorithm\ShortestPath\AllPairs\FloydWarshall;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class FloydWarshallTest extends TestCase
{
    public static $graphs;

    public static function setUpBeforeClass()
    {
        $vertex = array();

        self::$graphs = array();

        self::$graphs[0] = new Graph();
        self::$graphs[0]->createVertex('A');

        self::$graphs[1] = new Graph();
        $vertex[0] = self::$graphs[1]->createVertex('A');
        $vertex[1] = self::$graphs[1]->createVertex('B');
        $vertex[0]->createEdgeTo($vertex[1])->setWeight(1);

        self::$graphs[2] = new Graph();
        $vertex[2] = self::$graphs[2]->createVertex('A');
        $vertex[3] = self::$graphs[2]->createVertex('B');
        $vertex[4] = self::$graphs[2]->createVertex('C');
        $vertex[2]->createEdgeTo($vertex[3])->setWeight(1);
        $vertex[3]->createEdgeTo($vertex[4])->setWeight(2);

        self::$graphs[3] = new Graph();
        $vertex[5] = self::$graphs[3]->createVertex('A');
        $vertex[6] = self::$graphs[3]->createVertex('B');
        $vertex[7] = self::$graphs[3]->createVertex('C');
        $vertex[5]->createEdgeTo($vertex[6])->setWeight(1);
        $vertex[6]->createEdgeTo($vertex[7])->setWeight(3);
        $vertex[5]->createEdgeTo($vertex[7])->setWeight(3);

        // Will it work with negative weighted arcs???
        self::$graphs[4] = new Graph();
        $vertex[8] = self::$graphs[4]->createVertex('A');
        $vertex[8]->createEdgeTo($vertex[8])->setWeight(-1);

        self::$graphs[5] = new Graph();
        $vertex[9] = self::$graphs[5]->createVertex('1');
        $vertex[10] = self::$graphs[5]->createVertex('2');
        $vertex[11] = self::$graphs[5]->createVertex('3');
        $vertex[12] = self::$graphs[5]->createVertex('4');
        $vertex[9]->createEdgeTo($vertex[11])->setWeight(-2);
        $vertex[10]->createEdgeTo($vertex[11])->setWeight(3);
        $vertex[11]->createEdgeTo($vertex[12])->setWeight(2);
        $vertex[12]->createEdgeTo($vertex[10])->setWeight(-1);
        $vertex[10]->createEdgeTo($vertex[9])->setWeight(4);

        // And with a negative cycle??
        self::$graphs[6] = new Graph();
        $vertex[13] = self::$graphs[6]->createVertex('1');
        $vertex[14] = self::$graphs[6]->createVertex('2');
        $vertex[15] = self::$graphs[6]->createVertex('3');
        $vertex[13]->createEdgeTo($vertex[14])->setWeight(-1);
        $vertex[14]->createEdgeTo($vertex[15])->setWeight(-1);
        $vertex[15]->createEdgeTo($vertex[13])->setWeight(-1);

        // Empty Graphs
        self::$graphs[7] = new Graph();

        // Isolated Vertices graphs
        self::$graphs[8] = new Graph();

        $vertex[16] = self::$graphs[8]->createVertex('1');
        $vertex[17] = self::$graphs[8]->createVertex('2');
        $vertex[18] = self::$graphs[8]->createVertex('3');

        self::$graphs[9] = new Graph();
        $vertex[19] = self::$graphs[9]->createVertex('1');
        $vertex[20] = self::$graphs[9]->createVertex('2');
        $vertex[21] = self::$graphs[9]->createVertex('3');
        $vertex[22] = self::$graphs[9]->createVertex('4');
        $vertex[20]->createEdgeTo($vertex[21])->setWeight(1);
        $vertex[21]->createEdgeTo($vertex[22])->setWeight(2);
        $vertex[22]->createEdgeTo($vertex[21])->setWeight(1);
    }

    /**
     * Provider for test graphs and expected sequences of nodes for each resulting path(walk)
     * @return array
     */
    public function graphPathsProvider()
    {
        if(!self::$graphs)
           self::setUpBeforeClass();

        return array(
            array(
                self::$graphs[0],
                array('A' => array('A'))
            ),
            array(
                self::$graphs[1],
                array(
                    'A' => array('A', 'B'),
                    'B' => array('B')
                )
            ),
            array(
                self::$graphs[2],
                array(
                    'A' => array('A', 'B', 'C'),
                    'B' => array('B', 'C'),
                    'C' => array('C')
                )
            ),
            array(
                self::$graphs[3],
                array(
                    'A' => array('A', 'C'),
                    'B' => array('B', 'C'),
                    'C' => array('C')
                )
            ),
            array(
                self::$graphs[5],
                array(
                    '1' => array('1', '3', '4'),
                    '2' => array('2', '1', '3', '4'),
                    '3' => array('3', '4'),
                    '4' => array('4')
                )
            )
        );
    }

    /**
     * Provider for empty graph tests.
     * @return array
     */
    public function emptyGraphProvider()
    {
        if(!self::$graphs)
            self::setUpBeforeClass();

        return array(
            array(self::$graphs[7])
        );
    }

    /**
     * Provider for negative cycle graph tests.
     * @return array
     */
    public function negativeCycleGraphProvider()
    {
        if(!self::$graphs)
            self::setUpBeforeClass();

        return array(
            array(self::$graphs[6]),
            array(self::$graphs[4])
        );
    }

    /**
     * Provider for isolated vertices graph tests.
     * @return array
     */
    public function isolatedVerticesGraphProvider()
    {
        if(!self::$graphs)
            self::setUpBeforeClass();

        return array(
            array(self::$graphs[8], 0),
            array(self::$graphs[9], 3)
        );
    }

    protected function createAlg(Graph $graph)
    {
        return new FloydWarshall($graph);
    }

    /**
     * @dataProvider emptyGraphProvider
     */
    public function testEmptyGraph($testGraph)
    {
        $alg = $this->createAlg($testGraph);
        $result = $alg->createResult()->createGraph();
        $this->assertTrue($result->isEmpty(), 'The graph generated by Floyd Warshall over an empty graph must be an empty graph');
    }

    /**
     * @dataProvider isolatedVerticesGraphProvider
     */
    public function testIsolatedVertices($testGraph, $expectedEdgeNumber)
    {

        $alg = $this->createAlg($testGraph);
        $result = $alg->createResult();
        $result = $result->createGraph();
        $this->assertEquals($testGraph->getNumberOfVertices(), $result->getNumberOfVertices(), 'The number of vertices must be equal.');
        $this->assertEquals($expectedEdgeNumber, $result->getNumberOfEdges(), 'The number of edges must be ' . $expectedEdgeNumber);
    }

    /**
     * Testing resulting Paths for a Floyd Warshall algorithm
     *
     * @dataProvider graphPathsProvider
     */
    public function testResultingPath($testGraph, $expectedNodePairSequence)
    {
        $alg = $this->createAlg($testGraph);
        $result = $alg->createResult()->getPaths();

        foreach ($result as $idxNode => $path) {

            $seq = $path->getVerticesSequenceId();
            $this->assertTrue($expectedNodePairSequence[$idxNode] == $seq);
        }

    }

    /**
     * Testing the Exception in case of a negative cycle.
     *
     * @dataProvider negativeCycleGraphProvider
     * @expectedException UnexpectedValueException
     */
    public function testNegativeCycles($testGraph)
    {
        $alg = $this->createAlg($testGraph);
        $alg->createResult();
    }
}