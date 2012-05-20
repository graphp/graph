<?php

abstract class AlgorithmSp extends Algorithm {
    /**
     * start vertex to build shortest paths to
     * 
     * @var Vertex
     */
    protected $startVertex;
    
    public function __construct(Vertex $startVertex){
    	$this->startVertex = $startVertex;
    }
    
    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param Vertex     $endVertex
     * @throws Exception
     * @return array[Edge]
     * @uses AlgorithmSp::getEdges()
     * @uses AlgorithmSp::getEdgesToInternal()
     */
    public function getEdgesTo($endVertex){
    	return $this->getEdgesToInternal($endVertex,$this->getEdges());
    }
    
    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param Vertex $endVertex
     * @param array  $edges     array of all input edges to operate on
     * @throws Exception
     * @return array[Edge]
     * @uses AlgorithmSp::getEdges() if no edges were given
     */
    protected function getEdgesToInternal($endVertex,$edges){
    	$currentVertex = $endVertex;
    	$path = array();
    	while($currentVertex !== $this->startVertex){
    		$pre = NULL;
    		foreach($edges as $edge){ // check all edges to search for edge that points TO current vertex
    			try{
    				$pre = $edge->getVertexFromTo($currentVertex); // get start point of this edge (fails if current vertex is not its end point)
    				$path []= $edge;
    				$currentVertex = $pre;
    				break;
    			}
    			catch(Exception $ignore){
    			} // ignore: this edge does not point TO current vertex
    		}
    		if($pre === NULL){
    			throw new Exception('No edge leading to vertex');
    		}
    	}
    	return array_reverse($path);
    }
    
    /**
     * get sum of weight of given edges
     *
     * @param array[Edge] $edges
     * @return float
     * @uses Edge::getWeight()
     */
    private function sumEdges($edges){
    	$sum = 0;
    	foreach($edges as $edge){
    		$sum += $edge->getWeight();
    	}
    	return $sum;
    }
    
    /**
     * get map of vertex IDs to distance
     *
     * @return array[float]
     * @uses AlgorithmSp::getEdges()
     * @uses AlgorithmSp::getEdgesToInternal()
     * @uses AlgorithmSp::sumEdges()
     */
    public function getDistanceMap(){
    	$edges = $this->getEdges();
    	$ret = array();
    	foreach($this->startVertex->getGraph()->getVertices() as $vid=>$vertex){
    		try{
    			$ret[$vid] = $this->sumEdges($this->getEdgesToInternal($vertex,$edges));
    		}
    		catch(Exception $ignore){
    		} // ignore vertices that can not be reached
    	}
    	return $ret;
    }
    
    /**
     * get distance (sum of weights) between start vertex and given end vertex
     *
     * @param Vertex $endVertex
     * @return float
     * @throws Exception if given vertex is invalid or there's no path to given end vertex
     * @uses AlgorithmSp::getEdgesTo()
     * @uses AlgorithmSp::sumEdges()
     */
    public function getDistance($endVertex){
    	return $this->sumEdges($this->getEdgesTo($endVertex));
    }
    
    /**
     * create new resulting graph with only edges on shortest path
     *
     * @return Graph
     * @uses AlgorithmSp::getEdges()
     * @uses Graph::createGraphCloneEdges()
     */
    public function getResultGraph(){
    	return $this->startVertex->getGraph()->createGraphCloneEdges($this->getEdges());
    }
    
    /**
     * get cheapest edges (lowest weight) for given map of vertex predecessors
     * 
     * @param array[Vertex] $predecessor
     * @return array[Edge]
     * @uses Graph::getVertices()
     * @uses Vertex::getEdgesTo()
     * @uses Edge::getFirst()
     */
    protected function getEdgesCheapestPredecesor($predecessor){
        $vertices = $this->startVertex->getGraph()->getVertices();
        unset($vertices[$this->startVertex->getId()]);                          //start vertex doesn't have a predecessor
        
        $edges = array();
        foreach($vertices as $vid=>$vertex){
        	//echo $vertex->getId()." : ".$this->startVertex->getId()."\n";
        	if (isset( $predecessor[$vid] )){
        		$predecesVertex = $predecessor[$vid];	//get predecor
        
        		//echo "EDGE FROM ".$predecesVertex->getId()." TO ".$vertex->getId()." WITH COSTS: ".$totalCostOfCheapestPathTo[$vertex->getId()]."\n";
        
        		$edges []= Edge::getFirst($predecesVertex->getEdgesTo($vertex),Edge::ORDER_WEIGHT);	//get cheapest edge
        	}
        }
        
        return $edges;
    }
    
    /**
     * get all edges on shortest path for this vertex
     * 
     * @return array[Edge]
     */
    abstract public function getEdges();
}
