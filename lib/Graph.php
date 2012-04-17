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
	 * Return string with graph visualisation
	 *
	 * @return string
	 */
	public function toString(){
		$return = "Vertices of graph:\n";
	
		foreach ($this->vertices as $vertex){
			$return .= "\t".$vertex->toString()."\n";
		}
	
		return $return;
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
	
	/**
	 * get next free/unused/available vertex ID
	 * 
	 * its guaranteed there's NO other vertex with a greater ID
	 * 
	 * @return int
	 */
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
			throw new Exception('Vertex '.$id.' does not exist');
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
	
	/**
	 * return number of vertices (implements Countable, allows calling count($graph))
	 * 
	 * @return int
	 * @see Countable::count()
	 */
	public function count(){
	    return count($this->vertices);
	}
	
	/**
	 * return number of vertices
	 * 
	 * @return int
	 */
	public function getSize(){
	    return count($this->vertices);
	}
	
	/**
	 * get degree for k-regular-graph (only if each vertex has the same degree)
	 * 
	 * @return int
	 * @throws Exception if graph is not regular (i.e. vertex degrees are not equal)
	 * @uses Vertex::getIndegree()
	 * @uses Vertex::getOutdegree()
	 */
	public function getDegree(){
	    $anyVertex = reset($this->vertices); // get any start vertex for initial degree (simply use first from list)
	    if($anyVertex === false){
	        throw new Exception('Empty graph with no vertices');
	    }
	    $degree = $anyVertex->getIndegree(); // get initial degree to compare others to
	    
	    foreach($this->vertices as $vertex){
	        $i = $vertex->getIndegree();
	        
	        if($i !== $degree || $i !== $vertex->getOutdegree()){ // degree same (and for digraphs: indegree=outdegree)
	            throw new Exception('Graph is not k-regular');
	        }
	    }
	    
	    return $degree;
	}
	
	/**
	 * checks whether this graph is regular, i.e. each vertex has the same indegree/outdegree
	 * 
	 * @return boolean
	 * @uses Graph::getDegree()
	 */
	public function isRegular(){
	    try{
	        $this->getDegree();
	        return true;
	    }
	    catch(Exception $ignore){ }
	    return false;
	}
	
	/**
	 * check whether graph is consecutive (i.e. all vertices are connected)
	 * 
	 * @return boolean
	 * @see Graph::getNumberOfComponents()
	 * @uses AlgorithmConnectedComponents::isSingle()
	 */
	public function isConsecutive(){
	    $alg = new AlgorithmConnectedComponents($this);
	    return $alg->isSingle();
	}
	
	/**
	 * check whether this graph has an eulerian cycle
	 * 
	 * @return boolean
	 * @uses AlgorithmEulerian::hasCycle()
	 * @link http://en.wikipedia.org/wiki/Eulerian_path
	 */
	public function hasEulerianCycle(){
	    $alg = new AlgorithmEulerian($this);
	    return $alg->hasCycle();
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
	 * checks whether this graph is empty (no vertex - and thus no edges, aka null graph)
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
	 * checks whether the indegree of every vertex equals its outdegree
	 * 
	 * @return boolean
	 * @uses Vertex::getIndegree()
	 * @uses Vertex::getOutdegree()
	 */
	public function isBalanced(){
	    foreach($this->vertices as $vertex){
	        if($vertex->getIndegree() !== $vertex->getOutdegree()){
	            return false;
	        }
	    }
	    return true;
	}
	
	/**
	 * checks whether the graph has any directed edges (aka digraph)
	 * 
	 * @return boolean
	 */
	public function isDirected(){
	    foreach($this->edges as $edge){
	        if($eddge instanceof EdgeDirected){
	            return true;
	        }
	    }
	    return false;
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
	
	/**
	 * @return int number of components of this graph
	 */
	public function getNumberOfComponents(){
		$alg = new AlgorithmConnectedComponents($this);
		return $alg->getNumberOfComponents();
	}
}
