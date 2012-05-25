<?php
class AlgorithmMaxFlowEdmondsKarp extends Algorithm{

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
            throw new Exception('Start and destination must not be the same vertex');
        }
        if($startVertex->getGraph() !== $destinationVertex->getGraph()){
            throw new Exception('Start and target vertex have to be in the same graph instance');
        }
        $this->startVertex = $startVertex;
        $this->destinationVertex = $destinationVertex;
        $this->graph = $startVertex->getGraph();
    }
    
    /**
     * returns max flow graph
     *
     * @return Graph
     */
    public function getResultGraph(){
        $currentGraph = $this->graph->createGraphClone();
        
        do{
            $pathFlow = $this->getGraphShortestPathFlow($currentGraph);         // Get Shortest path if NULL-> Done
             
            if($pathFlow){
                 $currentGraph = $this->getResidualGraph($currentGraph, $pathFlow);
            }
        } while($pathFlow);

        //return flow sum of outgoing flows
        return $this->getFlowGraphFromResidualGraph($currentGraph);
        //return $currentGraph;
    }
    
    public function getMaxFlow(){
        $resultGraph = $this->getResultGraph();
        
        $start = $resultGraph->getVertex($this->startVertex->getId());
        $maxFlow = 0;
        foreach ($start->getOutgoingEdges() as $edge){
            $maxFlow = $maxFlow + $edge->getWeight();
        }
        return $maxFlow;
    }
    
    private function getFlowGraphFromResidualGraph($residualGraph){
        
        //run over original graph and create a new graph with the flow 
        $resultGraph=$this->graph->createGraphCloneEdgeless();
        
        $originalGraphEdgesArray=$this->graph->getEdges();
        foreach ($originalGraphEdgesArray as $edge){
            //For each edge of the residual graph, which goes in the opposite way
            //than the original edge, insert the edge reverted (residual edge) in the new graph
            
            $edge = $this->getEdgeCloned($edge,$residualGraph,true);
            
            $newFlowEdge = $resultGraph->createEdgeClone($edge);
            if($residualEdge){
                
                $newFlowEdge->setWeight($residualEdge->getWeight());
            }
            else{
                $newFlowEdge->setWeight(0);
            }
            
            //if not existing => remove the edge
            
        }
        return $resultGraph;
    }
    
    /**
     * get the shortest path flow (by count of edges)
     *
     * @param Graph $currentGraph
     * @return Graph if path exists OR NULL
     */
    private function getGraphShortestPathFlow($currentGraph)
    {
        
        $startVertex = $currentGraph->getVertex($this->startVertex->getId());
        
        // 1. Search _shortest_ (number of hops and cheapest) path from s -> t
        $breadthSearchAlg = new AlgorithmSearchBreadthFirst($startVertex);
        $path = $breadthSearchAlg->getGraphPathTo($currentGraph->getVertex($this->destinationVertex->getId()));
        
        if($path === NULL){
        	//no path found return null
        	return NULL;
        }
        
        // 2. get max flow from path
        $maxFlowValue = Edge::getFirst($path->getEdges(),Edge::ORDER_WEIGHT)->getWeight();
        if($maxFlowValue==0){
            //echo "stop flow value is 0\n";
            return null;
        }
         
        // 3. create graph with shortest path and max flow as edge values
        foreach($path->getEdges() as $edge){
            $edge->setWeight($maxFlowValue);
        }
         
        return $path;
    }

    /**
     * Returns creates from the currentgraph an a path a residual graph
     * 
     * @param Graph $currentGraph
     * @param Graph $path
     * @return Graph
     */
    private function getResidualGraph($currentGraph, $path)
    {
        // 1. Substract $path values from $graph
        foreach($path->getEdges() as $flowEdge){

            // find edge in original graph          
            $currentGraphEdge = $this->getEdgeCloned($flowEdge, $currentGraph);
        
            //lower the value of the original graph
            $currentGraphEdge->setWeight($currentGraphEdge->getWeight()-$flowEdge->getWeight()); //substract weight
            
            

            // 2. add in reversed direction of $path values to the $graph
            
            // Find out if reverse edge already exists
            $edgeArray=$currentGraphEndVertex->getEdgesTo($currentGraphStartVertex);
            $reverseEdge=array_shift($edgeArray);
            if(!isset($reverseEdge)){
                //no edge in reverese direction existing => create a new one
                $reverseEdge=$currentGraphEndVertex->createEdgeTo($currentGraphStartVertex);
                $reverseEdge->setWeight(0);
            };
            //add the weight to the reversed edge
            $reverseEdge->setWeight($reverseEdge->getWeight()+$flowEdge->getWeight());
                       
            //if the value of the original edge is 0, remove the edge
            if ($currentGraphEdge->getWeight()==0){
            	//$currentGraph->removeEdge($currentGraphEdge);
            	$currentGraphEdge->destroy();
            }
        }
        return $currentGraph;
    }
    
    private function getEdgeCloned($edge,$newGraph,$inverse=false){
        //get the Original vertices to find the proper edge
        $originalStartVertexArray = $edge->getStartVertices();
        $originalStartVertex = array_shift($originalStartVertexArray);
        
        $originalTargetVertexArray = $edge->getTargetVertices();
        $originalTargetVertex = array_shift($originalTargetVertexArray);
        
        if($inverse){
            $temp = $originalStartVertex;
            $originalStartVertex = $originalTargetVertex;
            $originalTargetVertex = $temp;
        }
        
        //get original vertices from resultgraph
        
        $residualGraphEdgeStartVertex = $newGraph->getVertex($originalStartVertex->getId());
        $residualGraphEdgeTargetVertex = $newGraph->getVertex($originalTargetVertex->getId());
        
        // now create the edge ; check if residual graph has backward edge
        $residualEdgeArray = $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);
        if(count($residualEdgeArray) !== 1){
            throw new Exception('More than one cloned edge? Parallel edges (multigraph) not supported');
        }
        
        return $residualEdgeArray[0];
    }
}
