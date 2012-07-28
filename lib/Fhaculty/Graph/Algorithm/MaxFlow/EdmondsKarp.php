<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge;
use Fhaculty\Graph\Algorithm\Base;
use Fhaculty\Graph\Algorithm\ResidualGraph;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchBreadthFirst;
use Fhaculty\Graph\Exception;

class EdmondsKarp extends Base{
    /**
     * @var Vertex
     */
    private $startVertex;

    /**
     * @var Vertex
     */
    private $destinationVertex;

    /**
     *
     * @param Vertex $startVertex the vertex where the flow search starts
     * @param Vertex $destinationVertex the vertex where the flow search ends (destination)
     */
    public function __construct(Vertex $startVertex, Vertex $destinationVertex){
        if($startVertex === $destinationVertex){
            throw new Exception\InvalidArgumentException('Start and destination must not be the same vertex');
        }
        if($startVertex->getGraph() !== $destinationVertex->getGraph()){
            throw new Exception\InvalidArgumentException('Start and target vertex have to be in the same graph instance');
        }
        $this->startVertex = $startVertex;
        $this->destinationVertex = $destinationVertex;
    }

    /**
     * Returns max flow graph
     *
     * @return Graph
     */
    public function createGraph(){
        $graphResult = $this->startVertex->getGraph()->createGraphClone();
        
        foreach ($graphResult->getEdges() as $edge){
        	$edge->setFlow(0);
        }
        
        do{
            $residualAlgorithm = new ResidualGraph($graphResult);
            $graphResidual = $residualAlgorithm->createGraph(true);        // Generate new residual graph and repeat
            
            $startVertex = $graphResidual->getVertex($this->startVertex->getId());
            $destinationVertex = $graphResidual->getVertex($this->destinationVertex->getId());
            
            // 1. Search _shortest_ (number of hops and cheapest) path from s -> t
            $breadthSearchAlg = new SearchBreadthFirst($startVertex);
            $pathFlow = $breadthSearchAlg->getGraphPathTo($destinationVertex);  // Get shortest path if NULL-> Done

            if($pathFlow){                                                        // If path exists add the new flow to graph
                // 2. get max flow from path
                $maxFlowValue = Edge::getFirst($pathFlow->getEdges(),Edge::ORDER_CAPACITY)->getCapacity();

                foreach ($pathFlow->getEdges() as $edge){
                    try{
                        $originalEdge = $graphResult->getEdgeClone($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() + $maxFlowValue);
                    }
                    catch(UnderflowException $e){
                        $originalEdge = $graphResult->getEdgeCloneInverted($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() - $maxFlowValue);
                    }
                }
            }

        } while($pathFlow);
        
        return $graphResult;
    }

    /**
     * Returns max flow value
     *
     * @return double
     */
    public function getFlowMax(){
        $resultGraph = $this->createGraph();
        
        $start = $resultGraph->getVertex($this->startVertex->getId());
        $maxFlow = 0;
        foreach ($start->getEdgesOut() as $edge){
            $maxFlow = $maxFlow + $edge->getFlow();
        }
        return $maxFlow;
    }
}
