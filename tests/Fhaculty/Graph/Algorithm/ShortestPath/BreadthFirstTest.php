<?php

use Fhaculty\Graph\Vertex;

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Algorithm\ShortestPath\BreadthFirst;
use Fhaculty\Graph\Loader\CompleteGraph;
use Fhaculty\Graph\Set\Vertices;

class BreadthFirstTest extends PHPUnit_Framework_TestCase
{
    public function testOne()
    {
        // TODO: this should NOT be part of the unit test
        // TODO: remove randomness
        $debug = false;

        $loader = new CompleteGraph(10);
        $loader->setEnableDirectedEdges(true);
        $graph = $loader->createGraph();

        // randomly remove 70% of the edges
        foreach (array_slice(Edge::getAll($graph->getEdges(), Edge::ORDER_RANDOM), 0, $graph->getNumberOfEdges()*0.8) as $edge) {
            $edge->destroy();
        }

        $start = $graph->getVertices()->getVertexOrder(Vertices::ORDER_RANDOM);
        $start->setLayoutAttribute('shape', 'doublecircle');

        // actually start breadth search
        $alg = new BreadthFirst($start);

        // visualize all resulting edges in blue
        foreach ($alg->getEdges() as $edge) {
            $edge->setLayoutAttribute('color', 'blue');
        }

        // visualize all reachable vertices in blue
        foreach ($alg->getVertices() as $vertex) {
            $vertex->setLayoutAttribute('color', 'blue');
        }

        if ($debug) {
            // visualize resulting graph
            $vis = new GraphViz($graph);
            $vis->display();
        }

        $this->assertTrue(count($alg->getVertices()) >= count($start->getVerticesEdgeTo()));

        // test all reachable vertices
        foreach ($alg->getDistanceMap() as $vid => $distance) {
            if($debug) echo 'vertex ' . $vid . ' in distance ' . $distance;
            $walk = $alg->getWalkTo($graph->getVertex($vid));

            $this->assertEquals($distance, $walk->getNumberOfEdges());

            if ($debug) {
                echo ' (vertex walk: ' . implode(', ', $walk->getVerticesSequenceId()) . ')';
                echo PHP_EOL;

                $vis = new GraphViz($walk->createGraph());
                $vis->display();
            }
        }

        // $alg = new BreadthFirst($startVertex);
    }
}
