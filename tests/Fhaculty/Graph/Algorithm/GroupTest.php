<?php

use Fhaculty\Graph\Algorithm\Groups as Groups;
use Fhaculty\Graph\Graph;

class GroupTest extends TestCase
{
    public function testGroupsTwo()
    {
        $graph = new Graph();
        $graph->createVertex('no group');
        $graph->createVertex('group 0')->setGroup(0);

        $alg = new Groups($graph);
        $this->assertEquals(2, $alg->getNumberOfGroups(), 'Empty group must differ from 0');

    }

    public function testGroupsOneAndTwo()
    {

        $graph = new Graph();
        $graph->createVertex('group 1')->setGroup(1);
        $graph->createVertex('group 2.1')->setGroup(2);
        $graph->createVertex('group 2.2')->setGroup(2);

        $alg = new Groups($graph);
        $this->assertEquals(2, $alg->getNumberOfGroups());

    }
}
