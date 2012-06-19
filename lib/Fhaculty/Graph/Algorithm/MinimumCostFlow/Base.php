<?php

namespace Fhaculty\Graph\Algorithm\MinimumCostFlow;

use Fhaculty\Graph\Edge;

use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Algorithm\Base as AlgorithmBase;
use Fhaculty\Graph\Graph;

abstract class Base extends AlgorithmBase {

    /**
     * Origianl graph
     * 
     * @var Graph
     */
    protected $graph;
    
    /**
     * The given graph where the algorithm should operate on
     * 
     * @param Graph $graph
     * @throws Exception if the given graph is not balanced
     */
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }
    
    /**
     * check if balance is okay and throw exception otherwise
     * 
     * @throws UnexpectedValueException
     * @return AlgorithmMCF $this (chainable)
     */
    protected function checkBalance(){
        $balance = $this->graph->getBalance();
        $tolerance = 0.000001;
        if($balance >= $tolerance || $balance <= -$tolerance){
            throw new UnexpectedValueException("The given graph is not balanced value is: ".$balance);
        }
        return $this;
    }
    
    /**
     * helper used to add $newFlow to original edges of $clonedEdges in graph $resultGraph
     * 
     * @param Graph       $resultGraph graph to look for original edges
     * @param array[Edge] $clonedEdges array of cloned edges to be modified
     * @param number      $newFlow     flow to add
     * @uses Graph::getEdgeClone()
     * @uses Graph::getEdgeCloneInverted()
     * @uses Edge::getFlow()
     * @uses Edge::setFlow()
     */
    protected function addFlow(Graph $resultGraph,$clonedEdges,$newFlow){
        foreach($clonedEdges as $clonedEdge){
            try {
            	$edge = $resultGraph->getEdgeClone($clonedEdge);                //get edge from clone
            	$edge->setFlow($edge->getFlow() + $newFlow);                    // add flow
            } catch(UnderflowException $ignore) {                              //if the edge doesn't exist => use the residual edge
            	$edge = $resultGraph->getEdgeCloneInverted($clonedEdge);
            	$edge->setFlow($edge->getFlow() - $newFlow);                    //remove flow
            }
        }
    }
    
    /**
     * calculate total weight along minimum-cost flow
     * 
     * @return float
     * @uses AlgorithmMCF::createGraph()
     * @uses Graph::getWeightFlow()
     */
    public function getWeightFlow(){
        return $this->createGraph()->getWeightFlow();
    }
    
    /**
     * create new resulting graph with minimum-cost flow on edges
     *
     * @throws Exception if the graph has not enough capacity for the minimum-cost flow
     * @return Graph
     */
    abstract public function createGraph();
}
