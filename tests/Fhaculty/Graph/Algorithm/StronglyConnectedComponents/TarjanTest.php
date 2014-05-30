<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use \Fhaculty\Graph\Algorithm\StronglyConnectedComponents\Tarjan;

class TarjanTest extends TestCase
{
    protected $graph;

    protected function setUp()
    {
        $this->graph = new Graph();
    }

    public function testTwoCycles()
    {
        // Build a graph
        for ($k = 0; $k < 6; $k++) {
            $this->graph->createVertex($k);
        }
        $vertex = $this->graph->getVertices()->getList();
        for ($offset = 0; $offset < 6; $offset += 3) {
            for ($k = 0; $k < 3; $k++) {
                $start = $vertex[$offset + $k];
                $end = $vertex[$offset + (($k + 1) % 3)];
                $start->createEdgeTo($end);
            }
        }

        // Run the algorithm
        // echo $this->graph->__toString(); die;
        $algorithm = new Tarjan($this->graph);

        $ret = $algorithm->getStronglyConnected();
        $this->assertCount(2, $ret, 'Two cycles');
        $this->assertCount(3, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }

    public function testCompleteGraph()
    {
        $card = 6;

        for ($k = 0; $k < $card; $k++) {
            $this->graph->createVertex($k);
        }
        foreach ($this->graph->getVertices()->getList() as $src) {
            foreach ($this->graph->getVertices()->getList() as $dst) {
                if ($src === $dst)
                    continue;
                $src->createEdgeTo($dst);
            }
        }

        // Run the algorithm
        // echo $this->graph->__toString(); die;
        $algorithm = new Tarjan($this->graph);

        $ret = $algorithm->getStronglyConnected();

        $this->assertCount(1, $ret, 'One SCC');
        $this->assertCount($card, $ret[0]);
    }

    public function testNotObviousGraph()
    {
        $cls = $this->graph->createVertex('class');
        $meth = $this->graph->createVertex('method');
        $param = $this->graph->createVertex('param');
        $impl = $this->graph->createVertex('impl');

        $cls->createEdgeTo($meth);
        $meth->createEdgeTo($impl);
        $impl->createEdgeTo($cls);
        $impl->createEdgeTo($param);
        $meth->createEdgeTo($param);

        // Run the algorithm
        // echo $this->graph->__toString(); die;
        $algorithm = new Tarjan($this->graph);

        $ret = $algorithm->getStronglyConnected();

        $this->assertCount(2, $ret);
        $this->assertCount(1, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }
} 