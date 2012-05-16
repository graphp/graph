<?php
class AlgorithmMaxFlowEdmondsKarp{

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
     * @param Vertex $destinationVertex the vertex where the flow search ends the destination
     */
    public function __construct(Vertex $startVertex, Vertex $destinationVertex){
        $this->startVertex = $startVertex;
        $this->destinationVertex = $destinationVertex;
        $this->graph = $startVertex->getGraph();
        $this->workingGraph = $this->graph->createGraphClone();
    }

    private function start(){
         
        
        $currentGraph = $this->mergeParallelEdges($this->workingGraph);         // remove parallel edges
        do{
            $pathFlow = $this->getGraphShortestPathFlow($currentGraph);         // Get Shortes path if NULL-> Done
             
            if($pathFlow){
                 $currentGraph = $this->getResidualGraph($currentGraph, $pathFlow);
            }
        } while($pathFlow);

        //return flow sum of outgoing flows
        return $this->getFlowGraphFromResidualGraph($currentGraph);
        //return $currentGraph;
    }


    private function getFlowGraphFromResidualGraph($residualGraph){
        //TODO generate flow $returnGraph from $residualGraph and $this->graph
        //run over original graph and create a new graph with the flow 
        echo "\nMissing function to remove not used edges and rotate the used edges.\n\n";
        
        $input=$this->graph;
        measure(function() use ($input){
            echo "bla";
            return $input;
        	
        },'bla');
    
        
        $resultGraph=$this->graph->createGraphCloneEdgeless();
        
        $originalGraphEdgesArray=$this->graph->getEdges();
        foreach ($originalGraphEdgesArray as $edge){
            //For each edge of the residual graph, which goes in the opposite way
            //than the original edge, insert the edge reverted residual edge in the new graph
            
            //get the Original vertices to find the propper edge
            $originalStartVertexArray = $edge->getStartVertices();
            $originalStartVertex = array_shift($originalStartVertexArray);
         
            $originalTargetVertexArray = $edge->getTargetVertices();
            $originalTargetVertex = array_shift($originalTargetVertexArray);
            
            //if vertices in resultgrpah not existing create them
           
            $residualGraphEdgeTargetVertex = $residualGraph->getVertex($originalStartVertex->getId());
            $residualGraphEdgeStartVertex = $residualGraph->getVertex($originalTargetVertex->getId());
           
            //vertices are existing now create the edge
            
            //check if residual graph has backward edge
            $residualEdgeArray= $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);
                     
            $residualEdge = array_shift($residualEdgeArray);
            
            $newFlowEdge = $resultGraph->createEdgeClone($edge);
            if($residualEdge){
                
                $newFlowEdge->setWeight($residualEdge->getWeight());
            }
            else{
                $newFlowEdge->setWeight(0);
            }
            
            
            //if not existing remove the edge
            
        }

        return $resultGraph;
    }




    private function mergeParallelEdges($currentGraph){
        //TODO 1. find and merge parallel edges
        // alg works only without prarallel edges
        return $currentGraph;
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
        
        // 1. Search shortest path from s -> t
       
//         measure(function() use ($currentGraph){
//         	return $currentGraph;
//         },'getGraphShortestPathFlow');
        $breadthSearchAlg = new AlgorithmSearchBreadthFirst($startVertex);
        $path = $breadthSearchAlg->getGraphPathTo($currentGraph->getVertex($this->destinationVertex->getId()));

//         measure(function() use ($path){        
//         	return $path;
//         },'getGraphShortestPathFlow');
        
        if(isset($path)){
            // 2. get max flow from path
            $maxFlowValue = $path->getMinEdgeValue();
            if($maxFlowValue==0){
                //echo "stop flow value is 0\n";
                return null;
            }
             
            // 3. create graph with shortest path and max flow as edge values
            $path->setAllEdgeWeights($maxFlowValue);
             
            return $path;
        }
        else {
            //no path found return null
            return NULL;
        }
    }

    /**
     * Returns creates from the currentgraph an a path a residual graph
     * @param unknown_type $currentGraph
     * @param unknown_type $path
     * @return unknown
     */
    
    private function getResidualGraph($currentGraph, $path)
    {
        // 1. Substract $path values from $graph
        foreach($path->getEdges() as $flowEdge){

            // find edge in original graph          
     
            $flowEdgeEndArray = $flowEdge->getTargetVertices(); 
            $flowEdgeEndVertex =  array_shift($flowEdgeEndArray);
            $currentGraphEndVertex = $currentGraph->getVertex($flowEdgeEndVertex->getId());

            $flowEdgeStartArray = $flowEdge->getVertexFromTo($flowEdgeEndVertex);
            $flowEdgeStartVertex = $flowEdgeStartArray;
            $currentGraphStartVertex= $currentGraph->getVertex($flowEdgeStartVertex->getId());

            
            //lower the value of the original graph
            $edgesToArray=$currentGraphStartVertex->getEdgesTo($currentGraphEndVertex);
            $currentGraphEdge = array_shift($edgesToArray);
        
            $currentGraphEdge->setWeight($currentGraphEdge->getWeight()-$flowEdge->getWeight()); //substract weight
            
            

            // 2. add in reversed direction of $path values to the $graph
            
            // Find out if reverse edge already exists
            $edgeArray=$currentGraphEndVertex->getEdgesTo($currentGraphStartVertex);
            $reverseEdge=array_shift($edgeArray);
            if(!isset($reverseEdge)){
                //no edge in reverese direction existing, create a new one
                $reverseEdge=$currentGraphEndVertex->createEdgeTo($currentGraphStartVertex);
                $reverseEdge->setWeight(0);
            };
            //add the weight to the reveresd edge
            $reverseEdge->setWeight($reverseEdge->getWeight()+$flowEdge->getWeight());
                       
            //if the value of the original edge is 0, remove the edge
            if ($currentGraphEdge->getWeight()==0){
            	//$currentGraph->removeEdge($currentGraphEdge);
            	$currentGraphEdge->destroy();
            }
        }
        return $currentGraph;
    }




    /**
     * returns max flow graph
     *
     * @return Graph
     */
    public function getResultGraph(){

        return $this->start();
    }
}
