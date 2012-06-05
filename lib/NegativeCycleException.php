<?php

class NegativeCycleException extends Exception{
	/**
	 * array of vertices in the cycle
	 * 
	 * @var array[Vertex]
	 */
    private $vertices;

	public function __construct($message,$vertices){
		parent::__construct($message,NULL,NULL);
		$this->vertices = $vertices;
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
		$edges = array();
		$first = NULL;
		$last = NULL;
		foreach($this->vertices as $vertex){
			if($first === NULL){    // skip first vertex as last is unknown
				$first = $vertex;
			}else{
				$edges []= Edge::getFirst($last->getEdgesTo($vertex),Edge::ORDER_WEIGHT); // cheapest edge between last vertex and this vertex
			}
			$last = $vertex;
		}
		$edges []= Edge::getFirst($last->getEdgesTo($first),Edge::ORDER_WEIGHT); // additional edge from last vertex to first vertex

		return $edges;
	}

	/**
	 * get total weight of cycle (sum all edges' weights)
	 *
	 * @return float negative total weight
	 */
	public function getWeight(){
		$sum = 0;
		foreach($this->getEdges() as $edge){
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
	 * @uses NegativeCycleException::getEdges()
	 * @uses Graph::createGraphCloneEdges()
	 */
	public function createGraph(){
	    $graph = $this->getGraph()->createGraphCloneEdges($this->getEdges());   // create new graph clone with only cycle edges
	    foreach($graph->getVertices() as $vid=>$vertex){                      // get all vertices
	        if(!isset($this->vertices[$vid])){
	            $vertex->destroy();                                             // remove those not present in the cycle (isolated vertices)
	        }
	    }
	    return $graph;
	}
}
