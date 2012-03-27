<?php

class Graph{
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


//getter setter
	
	/**
	 * adds a new Edge to the Graph
	 * 
	 * @param Edge $edge instance of the new Edge
	 * @return Edge given edge as-is (chainable)
	 * @uses Edge::getId()
	 */
	public function addEdge($edge){
		$this->edges[$edge->getId()] = $edge;
		return $edge;
	}
	
	/**
	 * create a new EdgeDirected,  add to the Graph and return
	 * 
	 * @param int $id identifier of the new Edge
	 * @return EdgeDirected
	 * @uses Graph::addEdge()
	 */
	public function addEdgeDirectedId($id){
		$edge = new EdgeDirected($id);
		return $this->addEdge($edge);
	}
	
	/**
	 * create a new EdgeUndirected, add to the Graph and return
	 * 
	 * @param int $id identifier of the new Edge
	 * @return EdgeUndirected
	 * @uses Graph::addEdge()
	 */
	public function addEdgeUndirectedId($id){
		$edge = new EdgeUndirected($id);
		return $this->addEdge($edge);
	}
	
	/**
	 * returns the Edge with identifier $id
	 * 
	 * @param int $id identifier of Edge
	 * @return Edge
	 * @throws Exception
	 */
	public function getEdge($id){
		if(!isset($this->edges[$id])){
			throw new Exception("Edge ".$id." doesn't exist");
		}
		return $this->edges[$id];
	}
	
	/**
	 * returns an array of all Edges
	 * 
	 * @return array[Edge]
	 */
	public function getEdgeArray(){
		return $this->edges;
	}
	
	/**
	 * adds a new Vertex to the Graph
	 * 
	 * @param Vertex|int|NULL $vertex instance of Vertex or new vertex ID to use
	 * @return Vertex (chainable)
	 * @uses Vertex::getId()
	 */
	public function addVertex($vertex=NULL){
	    if($vertex === NULL){    // no ID given
	        $vertex = max(array_keys($this->vertices))+1; // auto ID
	    }
	    if(!($vertex instanceof Vertex)){
	        $vertex = new Vertex($vertex,$this);
	    }
		$this->vertices[$vertex->getId()] = $vertex;
		return $vertex;
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
	public function getVertexArray(){
		return $this->vertices;
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
	
	public function searchDepthFirst($vertexId){
		
		$alg = new AlgorithSearchDepthFirst($this, $vertexId);
		
		return $alg->getResult();
	}
}
