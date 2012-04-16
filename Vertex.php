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
	 * @uses Vertex::addEdge()
	 * @uses Graph::addEdge()
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
	 * @uses Vertex::addEdge()
	 * @uses Graph::addEdge()
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
	 * remove the given edge from list of connected edges (should NOT be called manually)
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
	 * remove all edges saved in the vertice
	 *
	 */
	public function removeAllEdges(){
		unset($this->edges);
		$this->edges = array();
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
            if($edge->isConnection($this, $vertex)){
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
	 * get ALL edges attached to this vertex
	 * 
	 * @return array[Edge]
	 */
	public function getEdges(){
	    return $this->edges;
	}
	
	/**
	 * get edges FROM this vertex TO the given vertex
	 * 
	 * @param Vertex $vertex
	 * @return array[Edge]
	 * @uses Edge::hasVertexTo()
	 */
	public function getEdgesTo($vertex){
	    $ret = array();
	    foreach($this->edges as $edge){
	        if($edge->hasVertexTo($vertex)){
	            $ret[] = $edge;
	        }
	    }
	    return $ret;
	}
	
	/**
	 * get edges FROM the given vertex TO this vertex
	 *
	 * @param Vertex $vertex
	 * @return array[Edge]
	 * @uses Vertex::getEdgesTo()
	 */
	public function getEdgesFrom($vertex){
	    return $vertex->getEdgesTo($this);
	}
	
	/**
	 * get all vertices this vertex has an edge to
	 * 
	 * @return array[Vertex]
	 * @uses Edge::getVerticesToFrom()
	 */
	public function getVerticesEdgeTo(){
	    $ret = array();
	    foreach($this->edges as $edge){
	        $vertex = $edge->getVertexToFrom($this);
	        $ret[$vertex->getId()] = $vertex;
	    }
	    return $ret;
	}
	
	/**
	 * get all vertices that have an edge TO this vertex
	 * 
	 * @return array[Vertex]
	 * @uses Edge::getVerticesFromTo()
	 */
    public function getVerticesEdgeFrom(){
	    $ret = array();
	    foreach($this->edges as $edge){
	        $vertex = $edge->getVerticesFromTo($this);
            $ret[$vertex->getId()] = $vertex;
	    }
	    return $ret;
	}
	
	/**
	 * get degree of this vertex (number of edges)
	 * 
	 * vertex degree is NOT defined for directed graphs (digraphs) and will
	 * throw an exception! use indegree and outdegree instead
	 * 
	 * @return int
	 * @throws Exception
	 * @see Vertex::getIndegree()
	 * @see Vertex::getOutdegree()
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
	
	/**
	 * check whether this vertex is isolated (i.e. has no edges attached)
	 * 
	 * @return boolean
	 */
	public function isIsolated(){
	    return !$this->edges;
	}
	
	/**
	 * check whether this is a leaf node (i.e. has only one edge)
	 * 
	 * @return boolean
	 * @throws Exception if this is directed graph
	 * @uses Vertex::getDegree()
	 * @todo check logic! should be indegree=1 and outdegree=0 for directed and degree=indegree=outdegree=1 for undirected?
	 */
	public function isLeaf(){
	    return ($this->getDegree() === 1);
	}
	
	/**
	 * get indegree of this vertex (number of edges TO this vertex)
	 * 
	 * @return int
	 * @uses Edge::hasVertexTo()
	 * @see Vertex::getDegree()
	 */
	public function getIndegree(){
	    $n = 0;
	    foreach($this->edges as $edge){
	        if($edge->hasVertexTo($this)){
	            ++$n;
	        }
	    }
	    return $n;
	}
	
	/**
	 * get outdegree of this vertex (number of edges FROM this vertex TO other vertices)
	 * 
	 * @return int
	 * @uses Edge::hasVertexFrom()
	 * @see Vertex::getDegree()
	 */
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
	 * @uses AlgorithmSearchBreadthFirst::getVertices()
	 */
	public function searchBreadthFirst(){
	    $alg = new AlgorithmSearchBreadthFirst($this);
	    return $alg->getVertices();
	}
	
	/**
	 * Depth-first search (recursive DFS)
	 * 
	 * @return array[Vertex]
	 * @uses AlgorithmSearchDepthFirst::getVertices()
	 */
	public function searchDepthFirst(){
		$alg = new AlgorithmSearchDepthFirst($this);
		return $alg->getVertices();
	}
}
