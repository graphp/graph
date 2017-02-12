<?php

use Fhaculty\Graph\Graph;

use Fhaculty\Graph\Algorithm\CycleFinder;
use Fhaculty\Graph\Algorithm\StronglyConnectedComponents\Tarjan;
use Fhaculty\Graph\Walk;

class CycleFinderTest extends TestCase
{
    public function testGraphHaSCycle()
    {
        $graph = new Graph();

        $A = $graph->createVertex("A");
        $B = $graph->createVertex("B");
        $C = $graph->createVertex("C");

        $A->createEdgeTo($B);
        $B->createEdgeTo($C);
        $C->createEdgeTo($A);
        $B->createEdgeTo($A);

        // $tarjan = new Tarjan($graph);
        // $componentsVertices = $tarjan->getStronglyConnected();
        // foreach($componentsVertices as $componentVertices){
        // $component = $graph->createGraphCloneVertices($componentVertices);

        $find = new CycleFinder($graph);
        $shortest = $find->getShortestCycle();

        $walk = Walk::factoryCycleFromVertices(array($A, $B, $A));
        $this->assertSame($walk->__toString(), $shortest->__toString());
    }
} 