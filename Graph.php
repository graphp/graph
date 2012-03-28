<?php

class Graph implements Countable{
    /**
     * array of all edges in this graph
     * 
     * @var array[Edge]
     */
	private $edges = array();
    
	/**
	 * array of all vertices in this graph
	 * 
	 * @var array[Vertex]
	 */
	private $vertices = array();
	
	/**
	 * create a new Vertex in the Graph
	 * 
	 * @param int|NULL $vertex instance of Vertex or new vertex ID to use
	 * @return Vertex (chainable)
	 * @uses Vertex::getId()
	 */
	public function createVertex($id=NULL){
	    if($id === NULL){    // no ID given
	        $id = $this->getNextId();
	    }
	    if(isset($this->vertices[$id])){
	        throw new Exception('ID must be unique');
	    }
	    $vertex = new Vertex($id,$this);
		$this->vertices[$id] = $vertex;
		return $vertex;
	}
	
	/**
	 * create the given number of vertices
	 * 
	 * @param int $n
	 * @return Graph (chainable)
	 * @uses Graph::getNextId()
	 */
	public function createVertices($n){
	    for($id=$this->getNextId(),$n+=$id;$id<$n;++$id){
	        $this->vertices[$id] = new Vertex($id,$this);
	    }
	    return $this;
	}
	
	private function getNextId(){
	    if(!$this->vertices){
	        return 0;
	    }
	    return max(array_keys($this->vertices))+1; // auto ID
	}
	
	/**
	 * returns the Vertex with identifier $id
	 * 
	 * @param int|string $id identifier of Vertex
	 * @return Vertex
	 * @throws Exception
	 */
	public function getVertex($id){
		if( ! isset($this->vertices[$id]) ){
			throw new Exception('Given Vertex does not exist');
		}
		
		return $this->vertices[$id];
	}
	
	/**
	 * returns an array of all Vertices
	 * 
	 * @return array[Vertex]
	 */
	public function getVertices(){
		return $this->vertices;
	}
	
	public function count(){
	    return count($this->vertices);
	}
	
	/**
	 * check whether graph is consecutive
	 * 
	 * @return boolean
	 * @todo
	 */
	public function isConsecutive(){
		throw new Exception('Unsupported');
		
		foreach($this->edges as $edge){
		    if(true){
		        return false;
		    }
		}
		return true;
	}
	
	/**
	 * checks whether this graph is trivial (one vertex and no edges)
	 * 
	 * @return boolean
	 */
	public function isTrivial(){
	    return (!$this->edges && count($this->vertices) === 1);
	}
	
	/**
	 * checks whether this graph is empty (no vertex - and thus no edges)
	 * 
	 * @return boolean
	 */
	public function isEmpty(){
	    return !$this->vertices;
	}
	
	/**
	 * checks whether this graph has no edges
	 * 
	 * @return boolean
	 */
	public function isEdgeless(){
	    return !$this->edges;
	}
	
	/**
	 * checks whether this graph is complete (every vertex has an edge to any other vertex)
	 * 
	 * @return boolean
	 * @uses Vertex::hasEdgeTo()
	 */
	public function isComplete(){
	    $c = $this->vertices;                                                   // copy of array (separate iterator but same vertices)
	    foreach($this->vertices as $vertex){                                    // from each vertex
	        foreach($c as $other){                                              // to each vertex
	            if($other !== $vertex && !$vertex->hasEdgeTo($other)){          // missing edge => fail
	                return false;
	            }
	        }
	    }
	    return true;
	}
	
	/**
	 * adds a new Edge to the Graph (should NOT be called manually!)
	 *
	 * @param Edge $edge instance of the new Edge
	 * @return Edge given edge as-is (chainable)
	 * @private
	 */
	public function addEdge($edge){
	    $this->edges[] = $edge;
	    return $edge;
	}
	
	/**
	 * returns an array of all Edges
	 *
	 * @return array[Edge]
	 * @todo purpose? REMOVE ME?
	 * @private
	 */
	public function getEdges(){
	    return $this->edges;
	}
	
	public function searchDepthFirst($vertexId){
		
		$alg = new AlgorithSearchDepthFirst($this, $vertexId);
		
		return $alg->getResult();
	}
}
