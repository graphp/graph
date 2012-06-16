<?php

use Fhaculty\Graph\Graph;

class GraphTest extends PHPUnit_Framework_TestCase
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
    
    public function testCanCreateVertex(){
        $this->graph->createVertex(0);
    }

// commented out because expecting base Exception is not allowed
//     /**
//      * @expectedException Exception
//      */
//     public function testCanNotCreateVertexDuplicateId(){
//         $this->graph->createVertex(1);
//         $this->graph->createVertex(1);
//     }
    
    
}
