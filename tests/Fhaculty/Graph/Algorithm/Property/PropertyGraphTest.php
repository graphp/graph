<?php

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Algorithm\Property\GraphProperty;

class PropertyGraphTest extends TestCase
{
    public function testEmptyIsEdgeless()
    {
        $graph = new Graph($graph);

        $alg = new GraphProperty($graph);

        $this->assertTrue($alg->isEdgeless());
        $this->assertFalse($alg->isTrivial());
    }

    public function testSingleVertexIsTrivial()
    {
        $graph = new Graph();
        $graph->createVertex(1);

        $alg = new GraphProperty($graph);

        $this->assertTrue($alg->isTrivial());
    }
}
