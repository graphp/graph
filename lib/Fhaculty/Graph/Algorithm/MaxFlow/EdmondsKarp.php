<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\EdgeDirected;

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
        
        // initialize null flow and check edges
        foreach ($graphResult->getEdges() as $edge){
            if(!($edge instanceof EdgeDirected)){
                throw new UnexpectedValueException('Undirected edges not supported for edmonds karp');
            }
        	$edge->setFlow(0);
        }
        
        $idA = $this->startVertex->getId();
        $idB = $this->destinationVertex->getId();
        
        do{
            // Generate new residual graph and repeat
            $residualAlgorithm = new ResidualGraph($graphResult);
            $graphResidual = $residualAlgorithm->createGraph();
            
            // 1. Search _shortest_ (number of hops and cheapest) path from s -> t
            $breadthSearchAlg = new SearchBreadthFirst($graphResidual->getVertex($idA));
            $pathFlow = $breadthSearchAlg->getGraphPathTo($graphResidual->getVertex($idB));

            if($pathFlow){                                                        // If path exists add the new flow to graph
                // 2. get max flow from path
                $maxFlowValue = Edge::getFirst($pathFlow->getEdges(),Edge::ORDER_CAPACITY)->getCapacity();

                // 3. add flow to path
                foreach ($pathFlow->getEdges() as $edge){
                    try{ // try to look for forward edge to increase flow
                        $originalEdge = $graphResult->getEdgeClone($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() + $maxFlowValue);
                    }
                    catch(UnderflowException $e){ // forward edge not found, look for back edge to decrease flow
                        $originalEdge = $graphResult->getEdgeCloneInverted($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() - $maxFlowValue);
                    }
                }
            }

        } while($pathFlow); // repeat while we still finds paths with residual capacity to add flow to
        
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
