<?php

class Vertex{
	private $id;
	
	/**
	 * @var array[Edge]
	 */
	private $edges = array();
	
	/**
	 * @var Graph
	 */
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
	public function hasPathTo($vertex){			//Tobias: I think this will be a algorithm later in the lecture
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
	    return $this->graph->addEdge($this->addEdge($vertex->addEdge(new EdgeUndirected($this,$vertex))));
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
	        throw new Exception('Given edge does NOT exist');				//Tobias: if edge gets Id => output of id
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
	 * 
	 * @throws Exception
	 */
	public function getDegree(){
	    $n = 0;
	    foreach($this->edges as $edge){
	        if($edge instanceof EdgeDirected){
	            throw new Exception('Degree not supported for directed edges');
	        }
	        ++$n;
	    }
	    return $n;
	}
	
	public function isIsolated(){
	    return !$this->edges;
	}
	
	public function isLeaf(){
	    return ($this->getDegree() === 1);
	}
	
	public function getIndegree(){
	    $n = 0;
	    foreach($this->edges as $edge){
	        if($edge->hasVertexTo($this)){
	            ++$n;
	        }
	    }
	    return $n;
	}
	
	public function getOutdegree(){
	    $n = 0;
	    foreach($this->edges as $edge){
	        if($edge->hasVertexFrom($this)){
	            ++$n;
	        }
	    }
	    return $n;
	}
	
	/**
	 * checks whether this vertex is a source, i.e. its indegree is zero
	 * 
	 * @return boolean
	 * @uses Edge::hasVertexTo()
	 * @see Vertex::getIndegree()
	 */
	public function isSource(){
	    foreach($this->edges as $edge){
	        if($edge->hasVertexTo($this)){
	            return false;
	        }
	    }
	    return true; // reach this point: no edge to this vertex
	}
	
	/**
	 * checks whether this vertex is a sink, i.e. its outdegree is zero
	 * 
	 * @return boolean
	 * @uses Edge::hasVertexFrom()
	 * @see Vertex::getOutdegree()
	 */
	public function isSink(){
	    foreach($this->edge as $edge){
	        if($edge->hasVertexFrom($this)){
	            return false;
	        }
	    }
	    return true; // reach this point: no edge away from this vertex
	}
	
	/**
	 * Breadth-first search (BFS)
	 *
	 * @return array[Vertex]
	 */
	public function searchBreadthFirst(){
	    $alg = new AlgorithmSearchBreadthFirst($this);
	    return $alg->getVertices();
	}
	
	/**
	 * Depth-first search (recursive)
	 * $this is the starting vertex
	 * 
	 * @return array[Vertex]
	 */
	public function searchDepthFirst(){
		$alg = new AlgorithmSearchDepthFirst($this);
		return $alg->getResult();
	}
}
