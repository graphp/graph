<?php

use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Algorithm\ResidualGraph;
use Fhaculty\Graph\Graph;

class ResidualGraphTest extends PHPUnit_Framework_TestCase
{
    /**
     * test an edge with capacity unused
     */
    public function testEdgeUnused(){
    	$graph = new Graph();
    
    	$graph->createVertex(0)->createEdgeTo($graph->createVertex(1))->setFlow(0)
    	                                                              ->setCapacity(2)
    	                                                              ->setWeight(3);
    
    	$alg = new ResidualGraph($graph);
    	$residual = $alg->createGraph();
    
    	$this->assertEquals(2,$residual->getNumberOfVertices());
    	$this->assertEquals(1,$residual->getNumberOfEdges());
    
    	$edge = Edge::getFirst($residual->getEdges());
    
    	$this->assertEquals(2,$edge->getCapacity());
    	$this->assertEquals(0,$edge->getFlow());
    	$this->assertEquals(3,$edge->getWeight());
    }
    
    /**
     * test an edge with capacity completely used
     */
    public function testEdgeUsed(){
    	$graph = new Graph();
    
    	$graph->createVertex(0)->createEdgeTo($graph->createVertex(1))->setFlow(2)
    	                                                              ->setCapacity(2)
    	                                                              ->setWeight(3);
    
    	$alg = new ResidualGraph($graph);
    	$residual = $alg->createGraph();
    
    	$this->assertEquals(2,$residual->getNumberOfVertices());
    	$this->assertEquals(1,$residual->getNumberOfEdges());
    
    	$edge = Edge::getFirst($residual->getEdges());
    
    	$this->assertEquals(2,$edge->getCapacity());
    	$this->assertEquals(0,$edge->getFlow());
    	$this->assertEquals(-3,$edge->getWeight());
    }
    
    /**
     * test an edge with capacity remaining
     */
    public function testEdgePartial(){
        $graph = new Graph();
        
        $graph->createVertex(0)->createEdgeTo($graph->createVertex(1))->setFlow(1)
                                                                    ->setCapacity(2)
                                                                    ->setWeight(3);
        
        $alg = new ResidualGraph($graph);
        $residual = $alg->createGraph();
        
        $this->assertEquals(2,$residual->getNumberOfVertices());
        $this->assertEquals(2,$residual->getNumberOfEdges());
        
        $edgeRemain = Edge::getFirst($residual->getVertex(0)->getEdgesOut());
        
        $this->assertEquals(1,$edgeRemain->getCapacity());
        $this->assertEquals(0,$edgeRemain->getFlow());
        $this->assertEquals(3,$edgeRemain->getWeight());
        
        $edgeBack = Edge::getFirst($residual->getVertex(1)->getEdgesOut());
        
        $this->assertEquals(1,$edgeBack->getCapacity());
        $this->assertEquals(0,$edgeBack->getFlow());
        $this->assertEquals(-3,$edgeBack->getWeight());
    }
    
    /**
     * expect exception for undirected edges
     * @expectedException UnexpectedValueException
     */
    public function testInvalidUndirected(){
        $graph = new Graph();
        
        $graph->createVertex()->createEdge($graph->createVertex())->setFlow(1)
                                                                  ->setCapacity(2);
        
        $alg = new ResidualGraph($graph);
        $alg->createGraph();
    }
    
    /**
     * expect exception for edges with no flow
     * @expectedException UnexpectedValueException
     */
    public function testInvalidNoFlow(){
    	$graph = new Graph();
    
    	$graph->createVertex()->createEdgeTo($graph->createVertex())->setCapacity(1);
    
    	$alg = new ResidualGraph($graph);
    	$alg->createGraph();
    }
    
    /**
     * expect exception for edges with no capacity
     * @expectedException UnexpectedValueException
     */
    public function testInvalidNoCapacity(){
    	$graph = new Graph();
    
    	$graph->createVertex()->createEdgeTo($graph->createVertex())->setFlow(1);
    
    	$alg = new ResidualGraph($graph);
    	$alg->createGraph();
    }
    
}
