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
     *
     * @var Graph
     */
    private $graph;

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
        $this->graph = $startVertex->getGraph();
    }

    /**
     * Returns max flow graph
     *
     * @return Graph
     */
    public function createGraph(){
        $currentGraph = $this->graph->createGraphClone();
        
        foreach ($currentGraph->getEdges() as $edge){
        	$edge->setFlow(0);
        }
        
        $i = 0;
        do{
            $startVertex = $currentGraph->getVertex($this->startVertex->getId());
            $destinationVertex = $currentGraph->getVertex($this->destinationVertex->getId());
            
            // 1. Search _shortest_ (number of hops and cheapest) path from s -> t
            $breadthSearchAlg = new SearchBreadthFirst($startVertex);
            $pathFlow = $breadthSearchAlg->getGraphPathTo($destinationVertex);  // Get shortest path if NULL-> Done

            if($pathFlow){                                                        // If path exists add the new flow to graph                
                // 2. get max flow from path
                $maxFlowValue = Edge::getFirst($pathFlow->getEdges(),Edge::ORDER_CAPACITY)->getCapacity();

                // 3. adjust flow along path
                foreach ($pathFlow->getEdges() as $edge){
                    $originalEdge = $currentGraph->getEdgeClone($edge);
                    $originalEdge->setFlow($originalEdge->getFlow() + $maxFlowValue);
                }

                $residualAlgorithm = new ResidualGraph($currentGraph);
                $residualAlgorithm->setMergeParallelEdges(true);
                $currentGraph = $residualAlgorithm->createGraph(true);        // Generate new residual graph and repeat
            }

        } while($pathFlow);

        return $this->getFlowGraphFromResidualGraph($currentGraph);                // Generate the full flow graph from the final residual graph (handled internal: with the initialGraph)
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

    /**
     * Merges a residual graph with initial graph
     *
     * @param Graph $residualGraph
     * @return Graph graph with maximal flow
     */
    private function getFlowGraphFromResidualGraph(Graph $residualGraph){

        $resultGraph = $this->graph->createGraphCloneEdgeless();                // Process original graph and create a new graph that contains the flow

        $originalGraphEdgesArray = $this->graph->getEdges();

        // For every edge in the residual graph,
        // that has an inversed edge in the original graph:
        // Insert the inversed residual edge into the new graph
        foreach ($originalGraphEdgesArray as $edge){
            // get capacity of original edge
            try{
            	$capacity = $residualGraph->getEdgeCloneInverted($edge)->getCapacity();
            }
            catch(UnderflowException $ignore){
                $capacity = 0;
            }
            
            // Add inversed edge to return graph
            $resultGraph->createEdgeClone($edge)->setFlow($capacity);
        }
        return $resultGraph;
    }
}
