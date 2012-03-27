<?php

class Vertex{
	private $id;
	private $edges;
	
	/**
	 * Creates a Vertex
	 * 
	 * @param int   $id    Identifier (int, string, what you want) $id
	 * @param array $edges optional array of id's of edges
	 */
	public function __construct($id, $edges = array()){
		$this->id = $id;
		$this->edges = $edges;
	}
	
//getter setter
	
	/**
	 * returns id of this Vertex
	 * 
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Add an edge to the Vertex
	 * 
	 * @param int $edgeId id of edge
	 * @return Vertex $this (chainable)
	 * @throws Exception
	 */
	public function addEdgeId($edgeId){
		$edgeId = (int)$edgeId;
		if ( isset($this->edges[ $edgeId ]) ){
			throw new Exception("Edge is allready added");
		}
		
		$this->edges[$edgeId] = $edgeId;
		return $this;
	}
	
	/**
	 * removes an Edge of this Vertex
	 * 
	 * @param int $edge if of Edge  (EdgeDirected or EdgeUndirected)
	 * @return Vertex $this (chainable)
	 * @throws Exception
	 */
	public function removeEdgeId($edge){
	
		if ( ! isset($this->edges[ $edge ]) ){
			throw new Exception("Edge isn't added");
		}
	
		unset($this->edges[$edge]);
		return $this;
	}
	
	/**
	 * checks whether this start vertex has a path to the given target vertex
	 * 
	 * @param Vertex $vertex
	 * @return boolean
	 * @throws Exception
	 */
	public function hasPathTo($vertex){
	    throw new Exception('TODO');
	    return true;
	}
	
	/**
	 * checks whether the given vertex has a path TO THIS vertex
	 * 
	 * @param Vertex $vertex
	 * @return boolean
	 * @uses Vertex::hasPathTo()
	 */
	public function hasPathFrom($vertex){
	    return $vertex->hasPathTo($this);
	}
	
	/**
	 * add new directed edge from this start vertex to given target vertex
	 * 
	 * @param Vertex $vertex target vertex
	 * @return EdgeDirected
	 * @throws Exception
	 */
	public function addEdgeTo($vertex){
	    if(true){ // duplicate paths
	        throw new Exception('');
	    }
	    $edge = new EdgeDirected($id,$this,$vertex); // TODO:
	    $this->graph->addEdge($edge);
	    return $edge;
	}
	
	/**
	 * add new undirected edge between this vertex and given vertex
	 * 
	 * @param Vertex $vertex
	 * @return EdgeUndirected
	 * @throws Exception
	 */
	public function addEdge($vertex){
	    if(true){
	        throw new Exception('TODO');
	    }
	    $edge = new EdgeUndirected($id);
	    return $edge;
	}
	
	/**
	 * check whether this vertex has a direct edge to given $vertex
	 * 
	 * @param Vertex $vertex
	 * @return boolean
	 */
	public function hasEdgeTo($vertex){
	    foreach($this->getEdges() as $edge){
	        if($edge->getTarget() === $this){ // TODO:
	            return true;
	        }
	    }
	    return false;
	}
	
	/**
	 * check whether the given vertex has a direct edge to THIS vertex
	 * 
	 * @param Vertex $vertex
	 * @return boolean
	 * @uses Vertex::hasEdgeTo()
	 */
	public function hasEdgeFrom($vertex){
	    return $vertex->hasEdgeTo($this);
	}
	
	/**
	 * returns all edges of this Vertex
	 * 
	 * @return array[int]
	 */
	public function getEdgeIdArray(){
		return $this->edges;
	}
}
