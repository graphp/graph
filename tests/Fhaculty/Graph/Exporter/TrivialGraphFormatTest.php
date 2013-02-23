<?php

use Fhaculty\Graph\Exporter\TrivialGraphFormat;

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Loader\CompleteGraph;

class TrivialGraphFormatTest extends TestCase
{
    public function testEmpty()
    {
        $graph = new Graph();

        $exporter = new TrivialGraphFormat();
        $this->assertEquals('#' . PHP_EOL, $exporter->getOutput($graph));
    }

    public function testTrivial()
    {
        $graph = new Graph();
        $graph->createVertex('trivial');

        $expected = <<<END
1 trivial
#

END;

        $exporter = new TrivialGraphFormat();
        $this->assertEquals($expected, $exporter->getOutput($graph));
    }

    public function testSimpleDirected()
    {
        // a -> b
        // c
        $graph = new Graph();
        $graph->createVertex('a')->createEdgeTo($graph->createVertex('b'));
        $graph->createVertex('c');

        $expected = <<<END
1 a
2 b
3 c
#
1 2

END;

        $exporter = new TrivialGraphFormat();
        $this->assertEquals($expected, $exporter->getOutput($graph));
    }

    public function testSimpleUndirected()
    {
        // a -- b
        // c
        $graph = new Graph();
        $graph->createVertex('a')->createEdge($graph->createVertex('b'));
        $graph->createVertex('c');

        $expected = <<<END
1 a
2 b
3 c
#
1 2
2 1

END;

        $exporter = new TrivialGraphFormat();
        $this->assertEquals($expected, $exporter->getOutput($graph));
    }
}
