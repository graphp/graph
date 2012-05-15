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
         
        
        $currentGraph = $this->mergeParallelEdges($this->workingGraph);                // remove parallel edges
        do{
            $pathFlow = $this->getGraphShortestPathFlow($currentGraph);         // Get Shortes path if NULL-> Done
             
            if($pathFlow){
                 $currentGraph = $this->getResidualGraph($currentGraph, $pathFlow);
            }
        } while($pathFlow);

        return $this->getFlowGraphFromResidualGraph($currentGraph);
    }


    private function getFlowGraphFromResidualGraph($residualGraph){
        //TODO generate flow $returnGraph from $residualGraph and $this->graph
        //run over original graph and create a new graph with the flow 
        echo "\nMissing function to remove not used edges and rotate the used edges.\n\n";

        return $residualGraph;
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
        
        $startVertex= $currentGraph->getVertex($this->startVertex->getId());
        
        // 1. Search shortest path from s -> t
        $breadthSearchAlg = new AlgorithmSearchBreadthFirst($startVertex);
        $path = $breadthSearchAlg->getGraphPathTo($this->destinationVertex);

        
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
            	$currentGraph->removeEdge($currentGraphEdge);
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
