<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\ShortestPath\FloydWarshall;

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
        $vertex[0]->createEdgeTo($vertex[1]);
        $edges = $vertex[0]->getEdgesTo($vertex[1]);
        $edges[0]->setWeight(1);

        self::$graphs[2] = new Graph();
        $vertex[2] = self::$graphs[2]->createVertex('A');
        $vertex[3] = self::$graphs[2]->createVertex('B');
        $vertex[4] = self::$graphs[2]->createVertex('C');
        $vertex[2]->createEdgeTo($vertex[3]);
        $edges = $vertex[2]->getEdgesTo($vertex[3]);
        $edges[0]->setWeight(1);

        $vertex[3]->createEdgeTo($vertex[4]);
        $edges = $vertex[3]->getEdgesTo($vertex[4]);
        $edges[0]->setWeight(2);

        self::$graphs[3] = new Graph();
        $vertex[5] = self::$graphs[3]->createVertex('A');
        $vertex[6] = self::$graphs[3]->createVertex('B');
        $vertex[7] = self::$graphs[3]->createVertex('C');
        $vertex[5]->createEdgeTo($vertex[6]);
        $edges = $vertex[5]->getEdgesTo($vertex[6]);
        $edges[0]->setWeight(1);

        $vertex[6]->createEdgeTo($vertex[7]);
        $edges = $vertex[6]->getEdgesTo($vertex[7]);
        $edges[0]->setWeight(1);

        $vertex[5]->createEdgeTo($vertex[7]);
        $edges = $vertex[5]->getEdgesTo($vertex[7]);
        $edges[0]->setWeight(3);

        // Will it work with negative weighted arcs???
        self::$graphs[4] = new Graph();
        $vertex[8] = self::$graphs[4]->createVertex('A');
        $vertex[8]->createEdgeTo($vertex[8]);
        $edges = $vertex[8]->getEdgesTo($vertex[8]);
        $edges[0]->setWeight(-1);

        self::$graphs[5] = new Graph();
        $vertex[9] = self::$graphs[5]->createVertex('1');
        $vertex[10] = self::$graphs[5]->createVertex('2');
        $vertex[11] = self::$graphs[5]->createVertex('3');
        $vertex[12] = self::$graphs[5]->createVertex('4');
        $vertex[9]->createEdgeTo($vertex[11]);
        $edges = $vertex[9]->getEdgesTo($vertex[11]);
        $edges[0]->setWeight(-2);

        $vertex[10]->createEdgeTo($vertex[11]);
        $edges = $vertex[10]->getEdgesTo($vertex[11]);
        $edges[0]->setWeight(3);

        $vertex[11]->createEdgeTo($vertex[12]);
        $edges = $vertex[11]->getEdgesTo($vertex[12]);
        $edges[0]->setWeight(2);

        $vertex[12]->createEdgeTo($vertex[10]);
        $edges = $vertex[12]->getEdgesTo($vertex[10]);
        $edges[0]->setWeight(-1);

        $vertex[10]->createEdgeTo($vertex[9]);
        $edges = $vertex[10]->getEdgesTo($vertex[9]);
        $edges[0]->setWeight(4);


    }

    public function resultSizeProvider()
    {
        if(!self::$graphs)
           self::setUpBeforeClass();

        return array(
            array(
                self::$graphs[0]->getVertexFirst(),
                1,
                array(
                    'A' => 1
                ),
                array(
                    'A' => array(
                        'A' => 0
                    )
                )
            ),
            array(
                self::$graphs[1]->getVertexFirst(),
                2,
                array(
                    'A' => 2,
                    'B' => 2
                ),
                array(
                    'A' => array(
                        'A' => 0,
                        'B' => 1
                    ),
                    'B' => array(
                        'B' => 0
                    )
                )
            ),
            array(
                self::$graphs[2]->getVertexFirst(),
                3,
                array(
                    'A' => 3,
                    'B' => 3,
                    'C' => 3
                ),
                array(
                    'A' => array(
                        'A' => 0,
                        'B' => 1,
                        'C' => 2
                    ),
                    'B' => array(

                        'B' => 0,
                        'C' => 1
                    ),
                    'C' => array(
                        'C' => 0
                    )
                )
            ),
            array(
                self::$graphs[3]->getVertexFirst(),
                3,
                array(
                    'A' => 3,
                    'B' => 3,
                    'C' => 3
                ),
                array(
                    'A' => array(
                        'A' => 0,
                        'B' => 1,
                        'C' => 2
                    ),
                    'B' => array(

                        'B' => 0,
                        'C' => 1
                    ),
                    'C' => array(
                        'C' => 0
                    )
                )
            ),
            array(
                self::$graphs[4]->getVertexFirst(),
                1,
                array(
                    'A' => 1
                ),
                array(
                    'A' => array(
                        'A' => 1,
                    )
                )
            ),
            array(
                self::$graphs[5]->getVertexFirst(),
                4,
                array(
                    '1' => 4,
                    '2' => 4,
                    '3' => 4,
                    '4' => 4
                ),
                array(
                    '1' => array(
                        '1' => 0,
                        '2' => 3,
                        '3' => 1,
                        '4' => 2
                    ),
                    '2' => array(

                        '1' => 1,
                        '2' => 0,
                        '3' => 2,
                        '4' => 3
                    ),
                    '3' => array(
                        '1' => 3,
                        '2' => 2,
                        '3' => 0,
                        '4' => 1
                    ),
                    '4' => array(
                        '1' => 2,
                        '2' => 1,
                        '3' => 3,
                        '4' => 0
                    )
                )
            )
        );
    }

    protected function createAlg(Vertex $vertex)
    {
        return new FloydWarshall($vertex);
    }

    /**
     * @dataProvider resultSizeProvider
     */
    public function testExpectedSize($vertex, $nRows, $nColsByRow, $verticesByRowColumn)
    {
        $alg = $this->createAlg($vertex);
        $shortestPaths = $alg->getEdges();

        $t = count($shortestPaths);
        $this->assertEquals($nRows, $t, 'The row size is incorrect.');

        foreach ($nColsByRow as $key => $value) {

            $this->assertEquals($value, count($shortestPaths[$key]), 'The column size for row ' . $key . ' is incorrect.');
        }

        foreach ($verticesByRowColumn as $key => $row) {

            foreach ($row as $key2 => $val) {

                $this->assertEquals($val, count($shortestPaths[$key][$key2]), 'The vertices count for position ' . $key . ', ' .  $key2 . ' is incorrect.');
            }
        }
    }
}