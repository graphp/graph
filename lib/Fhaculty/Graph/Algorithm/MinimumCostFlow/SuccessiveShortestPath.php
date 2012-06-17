<?php

namespace Fhaculty\Graph\Algorithm\MinimumCostFlow;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge;
use Fhaculty\Graph\EdgeDirected;
use Fhaculty\Graph\Algorithm\ShortestPath\MooreBellmanFord as SpMooreBellmanFord;
use Fhaculty\Graph\Algorithm\ResidualGraph;
use Fhaculty\Graph\Algorithm\SearchBreadthFirst;
use \Exception;

class SuccessiveShortestPath extends Base {
    /**
     * @uses Vertex::getFlow()
     * @uses Graph::createGraphClone()
     * @uses AlgorithmResidualGraph::createGraph()
     * @uses AlgorithmSearchBreadthFirst::getVertices()
     * @uses AlgorithmSpMooreBellmanFord::getEdgesTo(Vertex $targetVertex)
     * 
     * @see AlgorithmMCF::createGraph()
     */
    public function createGraph() {
        $this->checkBalance();
        $resultGraph = $this->graph->createGraphClone();
        
        //initial balance to 0
        $vertices = $resultGraph->getVertices();
        foreach ($vertices as $vertex){
            $vertex->setBalance(0);
        }
        
        //initial flow of edges
        $edges = $resultGraph->getEdges();
        foreach ($edges as $edge){
            $flow = 0;                                                          //0 if weight of edge is positiv
            
            if ($edge->getWeight() < 0){                                        //maximal flow if weight of edge is negative
                $flow = $edge->getCapacity();
                
                if ($edge instanceof EdgeDirected){
                    $startVertex = $edge->getVertexStart();
                    $endVertex = $edge->getVertexEnd();
                    
                    $this->addBalance($startVertex, $flow);                     //add balance to start- and end-vertex
                    $this->addBalance($endVertex, - $flow);
                }
                else {
                    throw new Exception("Undirected Edges not suported");
                }
            }
            
            $edge->setFlow($flow);
        }
        
        while(true)                                                             //return or Exception insite this while
        {
            //create residual graph
            $algRG = new ResidualGraph($resultGraph);
            $residualGraph = $algRG->createGraph();
            
            //search for a source    
            try {
                $sourceVertex = $this->getVertexSource($residualGraph);
            }
            catch (Exception $ignore) {                                         //if no source is found the minimum-cost flow is found
                break;
            }
            
            //search for reachble sink from this source
            try {
                $targetVertex = $this->getVertexSink($sourceVertex);
            }
            catch (Exception $ignore){                                          //if no target is found the network has not enough capacity
                throw new Exception("The graph has not enough capacity for the minimum-cost flow");
            }
            
            //calculate shortest path between source- and target-vertex
            $algSP = new SpMooreBellmanFord($sourceVertex);
            $edgesOnFlow = $algSP->getEdgesTo($targetVertex);
            
            //calculate the maximal possible flow
                                                                                //new flow is the maximal possible flow for this path
            $newflow    =    $this->graph->getVertex($sourceVertex->getId())->getBalance() - $sourceVertex->getBalance();
            $targetFlow = - ($this->graph->getVertex($targetVertex->getId())->getBalance() - $targetVertex->getBalance());
            
            // get minimum of source and target
            if ($targetFlow < $newflow){
                $newflow = $targetFlow;
            }
            
            // get minimum of capacity remaining on path
            $minCapacity = Edge::getFirst($edgesOnFlow,Edge::ORDER_CAPACITY_REMAINING)->getCapacityRemaining();
            if ($minCapacity < $newflow){
                $newflow = $minCapacity;
            }
            
            //add the new flow to the path
            foreach ($edgesOnFlow as $clonedEdge){
                try {
            	    $edge = $resultGraph->getEdgeClone($clonedEdge);            //get edge from clone
            	    $edge->addFlow( $newflow );                                 //add flow
                } catch(Exception $ignor) {                                     //if the edge doesn't exists use the residual edge
                    $edge = $resultGraph->getEdgeClone($clonedEdge, true);
                    $edge->addFlow( - $newflow);                                //remove flow
                }
            }
            
            //add balance to source and remove for the sink
            $oriSourceVertex = $resultGraph->getVertex($sourceVertex->getId());
            $oriTargetVertex = $resultGraph->getVertex($targetVertex->getId());
            
            $this->addBalance($oriSourceVertex, $newflow);
            $this->addBalance($oriTargetVertex, - $newflow);
        }
        return $resultGraph;
    }
    
    /**
     * check if balance on each vertex of the given graph matches the original graph's
     * 
     * @param Graph $graph
     * @return boolean
     * @throws Exception if given graph is not a clone of the original graph (each vertex has to be present in both graphs)
     * @uses Graph::getNumberOfVertices()
     * @uses Graph::getBalanace()
     * @uses Graph::getVertex()
     */
    private function isBalanceReached(Graph $graph){
        if($graph->getNumberOfVertices() !== $this->graph->getNumberOfVertices()){
            throw new Exception('Given graph does not appear to be a clone of input graph');
        }
        foreach($this->graph->getVertices() as $vid=>$vertex){
            if($vertex->getBalance() !== $graph->getVertex($vid)->getBalance()){
                return false;
            }
        }
        return true;
    }
    
    /**
     * 
     * 
     * @param Graph $graph
     * @throws Exception if there is no left source vertex
     * 
     * @return Vertex a source vertex in the given graph
     */
    private function getVertexSource(Graph $graph){
        foreach($graph->getVertices() as $vid=>$vertex){
            if($this->graph->getVertex($vid)->getBalance() - $vertex->getBalance() > 0){
                return $vertex;
            }
        }
        throw new Exception('No source vertex found in graph');
    }
    
    /**
     * 
     * 
     * @param Vertex $source
     * @throws Exception if there is no reachable sink vertex
     * 
     * @return Vertex a sink-vertex that is reachable from the source
     */
    private function getVertexSink(Vertex $source){
        $algBFS = new SearchBreadthFirst($source);                             //search for reachable Vertices
        
        foreach($algBFS->getVertices() as $vid=>$vertex){
            if($this->graph->getVertex($vid)->getBalance() - $vertex->getBalance() < 0){
                return $vertex;
            }
        }
        throw new Exception('No sink vertex connected to given source vertex found');
    }
    
    private function addBalance(Vertex $vertex, $balance){
        $vertex->setBalance($vertex->getBalance() + $balance);
    }
}
