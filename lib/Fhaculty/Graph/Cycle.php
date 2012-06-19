<?php

namespace Fhaculty\Graph;

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
     * create new cycle instance from given predecessor map
     * 
     * @param array[Vertex] $predecessors map of vid => predecessor vertex instance
     * @param Vertex        $vertex       start vertex to search predecessors from
     * @param int           $by
     * @param boolean       $desc
     * @return Cycle
     * @throws Exception\UnderflowException
     * @see Edge::getFirst() for parameters $by and $desc
     * @uses Cycle::factoryFromVertices()
     */
    public static function factoryFromPredecessorMap($predecessors,$vertex,$by=Edge::ORDER_FIFO,$desc=false){
        /*$checked = array();
        foreach($predecessors as $vertex){
            $vid = $vertex->getId();
            if(!isset($checked[$vid])){
                
            }
        }*/
        
        //find a vertex in the cycle
        $vid = $vertex->getId();
        $startVertices = array();
        do{
        	$startVertices[$vid] = $vertex;
        
        	$vertex = $predecessors[$vid];
        	$vid = $vertex->getId();
        }while(!isset($startVertices[$vid]));
        
        //find negative cycle
        $vid = $vertex->getId();
        $vertices = array();                                                   // build array of vertices in cycle
        do{
        	$vertices[$vid] = $vertex;                                          // add new vertex to cycle
        
        	$vertex = $predecessors[$vid];                                      // get predecessor of vertex
        	$vid = $vertex->getId();
        }while(!isset($vertices[$vid]));                                      // continue until we find a vertex that's already in the circle (i.e. circle is closed)
        
        $vertices = array_reverse($vertices,true);                             // reverse cycle, because cycle is actually built in opposite direction due to checking predecessors
        
        return Cycle::factoryFromVertices($vertices,$by,$desc);
    }
    
    /**
     * create new cycle instance with edges between given vertices
     * 
     * @param array[Vertex] $vertices
     * @param int           $by
     * @param boolean       $desc
     * @return Cycle
     * @throws Exception\UnderflowException if no vertices were given
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
            throw new Exception\UnderflowException('No vertices given');
        }
        $edges []= Edge::getFirst($last->getEdgesTo($first),$by,$desc);         // additional edge from last vertex to first vertex
        
        return new Cycle($vertices,$edges);
    }
    
    /**
     * create new cycle instance with vertices connected by given edges
     * 
     * @param array[Edge] $edges
     * @param Vertex      $startVertex
     * @return Cycle
     */
    public static function factoryFromEdges(array $edges,Vertex $startVertex){
        $vertices = array($startVertex->getId() => $startVertex);
        foreach($edges as $edge){
            $vertex = $edge->getVertexToFrom($startVertex);
            $vertices[$vertex->getId()] = $vertex;
            $startVertex = $vertex;
        }
        
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
	 * @throws LogicException
	 */
	public function getGraph(){
	    foreach($this->vertices as $vertex){
	        return $vertex->getGraph();
	    }
	    throw new Exception\LogicException('No vertex found. Must not happen');
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
