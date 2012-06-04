<?php

class AlgorithmMCFSuccessiveShortestPath extends AlgorithmMCF {
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
        
        //initial flow of edges
        $edges = $resultGraph->getEdges();
        foreach ($edges as $edge){
            $flow = 0;                                                            //0 if weight of edge is positiv
            
            if ($edge->getWeight() < 0){                                        //maximal flow if weight of edge is negative
                $flow = $edge->getCapacity();
            }
            
            $edge->setFlow($flow);
        }
        
        while(true)                                                                //return or Exception insite this while
        {
            //create residual graph
            $algRG = new AlgorithmResidualGraph($resultGraph);
            $residualGraph = $algRG->createGraph();
            
            //search for a source    
            $vertices = $residualGraph->getVertices();
            
            $sourceVertex = null;
            foreach ($vertices as $vertex){                                        //forall (just use the first found)
                
                if ($vertex->getFlow() > 0){                                    //if flow of vertex is positiv => source
                    $sourceVertex = $vertex;
                    break;
                }
            }
            if ($sourceVertex === null){                                        //if no source is found the minimum-cost flow is found 
                return $resultGraph;
            }
            
            //search for reachble target from this source
            $algBFS = new AlgorithmSearchBreadthFirst($sourceVertex);            //search for reachable Vertices
            $vertices = $algBFS->getVertices();
            
            $targetVertex = null;
            foreach ($vertices as $vertex){                                        //forall (just use the first found)
                
                if ($vertex->getFlow() < 0){                                    //if flow of vertex is negative => target
                    $targetVertex = $vertex;
                    break;
                }
            }
            if ($targetVertex === null){                                        //if no target is found the network has not enough capacity
                throw new Exception("The graph has not enough capacity for the minimum-cost flow");
            }
            
            //calculate shortest path between source- and target-vertex
            $algSP = new AlgorithmSpMooreBellmanFord($sourceVertex);
            $edgesOnFlow = $algSP->getEdgesTo($targetVertex);
                                                                                //new flow is the maximal possible flow for this path
            $newflow    = $sourceVertex->getBalance() - $sourceVertex->getFlow();
            $targetFlow = - ($targetVertex->getBalance() - $targetVertex->getFlow());
            
            if ($newflow > $targetFlow){                                        //minimum of source and target
                $newflow = $targetFlow;
            }
            
            foreach ($edgesOnFlow as $edge){                                    //minimum of left capacity at path
                $edgeFlow = $edge->getCapacityRemaining();
                
                if ($newflow > $edgeFlow){
                    $newflow = $edgeFlow;
                }
            }
            
            //TODO Verändere den Fluss zwischen Quelle und Senke
            
        }
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
    
    private function getVertexSource(Graph $graph){
        foreach($graph->getVertices() as $vertex){
            if($this->graph->getVertex($vid)->getBalance() > $vertex->getBalance()){
                return $vertex;
            }
        }
        throw new Exception('No source vertex found in graph');
    }
    
    private function getVertexSink(Vertex $source){
        $algBFS = new AlgorithmSearchBreadthFirst($source);            //search for reachable Vertices
        
        foreach($algBFS->getVertices() as $vid=>$vertex){
            if($this->graph->getVertex($vid)->getBalance() < $vertex->getBalance()){
                return $vertex;
            }
        }
        throw new Exception('No sink vertex connected to given source vertex found');
    }
    
}