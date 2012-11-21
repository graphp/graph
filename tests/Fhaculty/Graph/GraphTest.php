<?php

use Fhaculty\Graph\Exception\RuntimeException;

use Fhaculty\Graph\Vertex;

use Fhaculty\Graph\Exception\OverflowException;

use Fhaculty\Graph\Graph;

class GraphTest extends TestCase
{
    public function setup(){
        $this->graph = new Graph();
    }
    
    public function testBalance(){
        $this->assertEquals(0,$this->graph->getBalance());
    }
    
    public function testGetWeightMin(){
        $this->assertEquals(NULL,$this->graph->getWeightMin());
    }
    
    /**
     * check to make sure we can actually create vertices with automatic IDs 
     */
    public function testCanCreateVertex(){
        $graph = new Graph();
        $vertex = $graph->createVertex();
        $this->assertInstanceOf('\Fhaculty\Graph\Vertex',$vertex);
    }
    
    /**
     * check to make sure we can actually create vertices with automatic IDs 
     */
    public function testCanCreateVertexId(){
        $graph = new Graph();
        $vertex = $graph->createVertex(11);
        $this->assertInstanceOf('\Fhaculty\Graph\Vertex',$vertex);
        $this->assertEquals(11,$vertex->getId());
    }
    
    /**
     * fail to create two vertices with same ID
     * @expectedException OverflowException
     */
    public function testFailDuplicateVertex(){
        $graph = new Graph();
        $graph->createVertex(33);
        $graph->createVertex(33);
    }
}
