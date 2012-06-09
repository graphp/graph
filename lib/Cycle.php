<?php

class Cycle{
	/**
	 * array of vertices in the cycle
	 * 
	 * @var array[Vertex]
	 */
    private $vertices;
    
    /**
     * array of edges in the cycle
     * 
     * @var array[Edge]
     */
    private $edges;
    
    /**
     * create new cycle instance with edges between given vertices
     * 
     * @param array[Vertex] $vertices
     * @param int           $by
     * @param boolean       $desc
     * @return Cycle
     * @throws Exception
     * @see Edge::getFirst() for parameters $by and $desc
     */
    public static function factoryFromVertices($vertices,$by=Edge::ORDER_FIFO,$desc=false){
        $edges = array();
        $first = NULL;
        $last = NULL;
        foreach($vertices as $vertex){
        	if($first === NULL){    // skip first vertex as last is unknown
        		$first = $vertex;
        	}else{
        		$edges []= Edge::getFirst($last->getEdgesTo($vertex),$by,$desc); // pick edge between last vertex and this vertex
        	}
        	$last = $vertex;
        }
        if($last === NULL){
            throw new Exception('No vertices given');
        }
        $edges []= Edge::getFirst($last->getEdgesTo($first),$by,$desc);         // additional edge from last vertex to first vertex
        
        return new Cycle($vertices,$edges);
    }

	private function __construct($vertices,$edges){
		$this->vertices = $vertices;
		$this->edges    = $edges;
	}

	/**
	 * get all vertices in the cycle (no duplicate vertices)
	 *
	 * @return array[Vertex] 1+ vertices
	 */
	public function getVertices(){
		return $this->vertices;
	}

	/**
	 * get IDs of all vertices in the cycle
	 *
	 * @return array[int]
	 */
	public function getVerticesId(){
		return array_keys($this->vertices);
	}

	/**
	 * get all edges in the cycle (A->B->C->A)
	 *
	 * @return array[Edge] 1+ edges
	 */
	public function getEdges(){
		return $this->edges;
	}

	/**
	 * get total weight of cycle (sum all edges' weights)
	 *
	 * @return float
	 */
	public function getWeight(){
		$sum = 0;
		foreach($this->edges as $edge){
			$sum += $edge->getWeight();
		}
		return $sum;
	}
	
	/**
	 * get original graph of this cycle
	 * 
	 * @return Graph
	 */
	public function getGraph(){
	    foreach($this->vertices as $vertex){
	        return $vertex->getGraph();
	    }
	    throw new Exception('Must not happen');
	}
	
	/**
	 * create new graph clone with only vertices and edges actually in the cycle
	 * 
	 * @return Graph
	 * @uses Cycle::getEdges()
	 * @uses Graph::createGraphCloneEdges()
	 */
	public function createGraph(){
	    $graph = $this->getGraph()->createGraphCloneEdges($this->edges);        // create new graph clone with only cycle edges
	    foreach($graph->getVertices() as $vid=>$vertex){                      // get all vertices
	        if(!isset($this->vertices[$vid])){
	            $vertex->destroy();                                             // remove those not present in the cycle (isolated vertices)
	        }
	    }
	    return $graph;
	}
}
