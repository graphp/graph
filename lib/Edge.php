<?php

abstract class Edge{
    /**
     * weight of this edge
     * 
     * @var float|int|NULL
     * @see Edge::getWeight()
     */
	protected $weight = NULL;
	
	/**
	 * get Vertices that are a target of this edge
	 *
	 * @return array[Vertex]
	 */
	abstract public function getTargetVertices();
	
	/**
	 * Return string with edge visualisation
	 *
	 * @return string
	 */
	abstract function toString();
	
// 	abstract public function hasVertexTo($vertex);
	
// 	abstract public function hasVertexFrom($vertex);
	
	abstract public function isConnection($from, $to);
	
	/**
	 * returns whether this edge is actually a loop
	 * 
	 * @return boolean
	 */
	abstract public function isLoop();
	
// 	abstract public function getVerticesTo();
	
	/**
	 * get target vertex we can reach with this edge from the given start vertex
	 *
	 * @param Vertex $startVertex
	 * @return Vertex
	 * @throws Exception if given $startVertex is not a valid start
	 * @see Edge::hasEdgeFrom() to check if given start is valid
	 */
	abstract public function getVertexToFrom($startVertex);
	
	/**
	 * get start vertex which can reach us(the given end vertex) with this edge
	 *
	 * @param Vertex $startVertex
	 * @return Vertex
	 * @throws Exception if given $startVertex is not a valid start
	 * @see Edge::hasEdgeFrom() to check if given start is valid
	 */
	abstract public function getVertexFromTo($endVertex);
	
// 	abstract public function getVerticesFrom();
    
	/**
	 * return weight of edge
	 * 
	 * @return float|int|NULL numeric weight of edge or NULL=not set
	 */
	public function getWeight(){
	    return $this->weight;
	}
	
	/**
	 * set new weight for edge
	 * 
	 * @param float|int|NULL $weight new numeric weight of edge or NULL=unset weight
	 * @return Edge $this (chainable)
	 */
	public function setWeight($weight){
	    if($weight !== NULL && !is_float($weight) && !is_int($weight)){
	        throw new Exception('Invalid weight given - must be numeric or NULL');
	    }
	    $this->weight = $weight;
	    return $this;
	}
	
	/**
	 * get all vertices this edge connects
	 * 
	 * @return array[Vertex]
	 */
	abstract public function getVertices();
	
	/**
	 * get IDs of all vertices this edge connects
	 * 
	 * @return array[int]
	 * @see Edge::getVertices()
	 */
	public function getVerticesId(){
	    $ret = $this->getVertices();
	    foreach($ret as &$v){
	        $v = $v->getId();
	    }
	    return $ret;
	}
	
	private function getGraph(){
	    foreach($this->getVertices() as $vertex){
	        return $vertex->getGraph();
	    }
	}
	
	/**
	 * destroy edge and remove reference from vertices and graph
	 * 
	 * @uses Graph::removeEdge()
	 * @uses Vertex::removeEdge()
	 */
	public function destroy(){
	    $this->getGraph()->removeEdge($this);
	    foreach($this->getVertices() as $vertex){
	        $vertex->removeEdge($this);
	    }
	}
	
	/**
	 * do NOT allow cloning of objects
	 * 
	 * @throws Exception
	 */
	private function __clone(){
	    throw new Exception();
	}
}
