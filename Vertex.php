<?php

class Vertex{
	private $id;
	private $edges = array();
	private $graph;
	
	/**
	 * Creates a Vertex
	 * 
	 * @param int   $id    Identifier (int, string, what you want) $id
	 * @param Graph $graph graph to be added to
	 */
	public function __construct($id, $graph){
		$this->id = $id;
		$this->graph = $graph;
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
	 * create new directed edge from this start vertex to given target vertex
	 * 
	 * @param Vertex $vertex target vertex
	 * @return EdgeDirected
	 * @throws Exception
	 */
	public function createEdgeTo($vertex){
	    return $this->graph->addEdge($this->addEdge($vertex->addEdge(new EdgeDirected($this,$vertex))));
	}
	
	/**
	 * add new undirected (bidirectional) edge between this vertex and given vertex
	 * 
	 * @param Vertex $vertex
	 * @return EdgeUndirected
	 * @throws Exception
	 */
	public function createEdge($vertex){
	    return $this->graph->addEdge($this->addEdge($vertex->addEdge(new EdgeDirected($this,$vertex))));
	}
	
	/**
	 * add given edge to list of connected edges (should NOT be called manually!)
	 * 
	 * @param Edge $edge
	 * @return Edge given $edge as-is
	 * @private
	 */
	public function addEdge($edge){
	    $this->edges []= $edge;
	    return $edge;
	}
	
	/**
	 * remove the given edge from list of connected edges
	 * 
	 * @param Edge $edge
	 * @return Edge given $edge as-is
	 * @private
	 */
	public function removeEdge($edge){
	    $id = array_search($edge,$this->edges,true);
	    if($id === false){
	        throw new Exception('Given edge does NOT exist');
	    }
	    unset($this->edges[$id]);
	    return $edge;
	}
	
	/**
	 * check whether this vertex has a direct edge to given $vertex
	 * 
	 * @param Vertex $vertex
	 * @return boolean
	 * @uses Edge::hasVertexTo()
	 */
	public function hasEdgeTo($vertex){
	    foreach($this->edges as $edge){
            if($edge->hasVertexTo($vertex)){
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
	
	public function getEdges(){
	    return $this->edges;
	}
	
	/**
	 * get all vertices this vertex has an edge to
	 * 
	 * @return array[Vertex]
	 * @uses Edge::getVerticesTo()
	 */
	public function getVerticesEdgeTo(){
	    $ret = array();
	    foreach($this->edges as $edge){
	        foreach($edge->getVerticesTo() as $vertex){
	            $ret[$vertex->getId()] = $vertex;
	        }
	    }
	    return $ret;
	}
	
	/**
	 * Breadth-first search (BFS)
	 *
	 * @return array[Vertex]
	 */
	public function searchBreadthFirst(){
	    $alg = new AlgorithmBreadthFirst($this);
	    return $alg->getVertices();
	}
}
