<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Algorithm\ConnectedComponents as AlgorithmConnectedComponents;
use Fhaculty\Graph\Algorithm\Bipartit as AlgorithmBipartit;
use Fhaculty\Graph\Algorithm\Eulerian as AlgorithmEulerian;
use Fhaculty\Graph\Algorithm\Groups as AlgorithmGroups;

class Graph extends Layoutable{
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
     * @param int|NULL $id              new vertex ID to use (defaults to NULL: use next free numeric ID)
     * @param boolean  $returnDuplicate normal operation is to throw an exception if given id already exists. pass true to return original vertex instead
     * @return Vertex (chainable)
     * @throws DomainException if given vertex $id is invalid
     * @throws RuntimeException if given vertex $id already exists and $returnDuplicate is not set
     * @uses Vertex::getId()
     */
    public function createVertex($id=NULL,$returnDuplicate=false){
        if($id === NULL){    // no ID given
            $id = $this->getNextId();
        }else if(!is_int($id) && !is_string($id)){
            throw new Exception\DomainException('Vertex ID has to be of type integer or string');
        }
        if(isset($this->vertices[$id])){
            if($returnDuplicate){
                return $this->vertices[$id];
            }
            throw new Exception\RuntimeException('ID must be unique');
        }
        $vertex = new Vertex($id,$this);
        $this->vertices[$id] = $vertex;
        return $vertex;
    }
    
    /**
     * create a new Vertex in this Graph from the given input Vertex of another graph
     * 
     * @param Vertex $originalVertex
     * @return Vertex new vertex in this graph
     * @throws RuntimeException if vertex with this ID already exists
     */
    public function createVertexClone($originalVertex){
        $id = $originalVertex->getId();
        if(isset($this->vertices[$id])){
            throw new Exception\RuntimeException('Id of cloned vertex already exists');
        }
        $newVertex = new Vertex($id,$this);
        // TODO: properly set attributes of vertex
        $newVertex->setLayoutRaw($originalVertex->getLayout());
        $newVertex->setBalance($originalVertex->getBalance());
        $newVertex->setGroup($originalVertex->getGroup());
        $this->vertices[$id] = $newVertex;
        return $newVertex;
    }
    
    /**
     * create new clone/copy of this graph - copy all attributes and vertices, but do NOT copy edges
     * 
     * using this method is faster than creating a new graph and calling createEdgeClone() yourself
     *
     * @return Graph
     */
    public function createGraphCloneEdgeless(){
        $graph = new Graph();
        $graph->setLayoutRaw($this->getLayout());
        // TODO: set additional graph attributes
        foreach($this->getVertices() as $originalVertex){
            $vertex = $graph->createVertexClone($originalVertex);
            //$graph->vertices[$vid] = $vertex;
        }
        return $graph;
    }
    
    /**
     * create new clone/copy of this graph - copy all attributes and vertices. but only copy all given edges
     *
     * @param array[Edge] $edges array of edges to be cloned
     * @return Graph
     * @uses Graph::createGraphCloneEdgeless()
     * @uses Graph::createEdgeClone() for each edge to be cloned
     */
    public function createGraphCloneEdges($edges){
        $graph = $this->createGraphCloneEdgeless();
        foreach($edges as $edge){
            $graph->createEdgeClone($edge);
        }
        return $graph;
    }
    
    /**
     * create new clone/copy of this graph - copy all attributes, vertices and edges
     *
     * @return Graph
     * @uses Graph::createGraphCloneEdges() to clone graph with current edges
     */
    public function createGraphClone(){
        return $this->createGraphCloneEdges($this->edges);
    }
    
    /**
     * create a new clone/copy of this graph - copy all attributes and given vertices and its edges
     * 
     * @param array[Vertex] $vertices array of vertices to keep
     * @return Graph
     * @uses Graph::createGraphClone() to create a complete clone
     * @uses Vertex::destroy() to remove unneeded vertices again
     */
    public function createGraphCloneVertices($vertices){
        $graph = $this->createGraphClone();
        foreach($graph->getVertices() as $vid=>$vertex){
            if(!isset($vertices[$vid])){
                $vertex->destroy();
            }
        }
        return $graph;
    }
    
    /**
     * create new clone of the given edge between adjacent vertices
     * 
     * @param Edge $originalEdge original edge (not neccessarily from this graph)
     * @return Edge new edge in this graph
     * @uses Graph::createEdgeCloneInternal()
     */
    public function createEdgeClone($originalEdge){
        return $this->createEdgeCloneInternal($originalEdge,0,1);
    }
    
    /**
     * create new clone of the given edge inverted (in opposite direction) between adjacent vertices
     *
     * @param Edge $originalEdge original edge (not neccessarily from this graph)
     * @return Edge new edge in this graph
     * @uses Graph::createEdgeCloneInternal()
     */
    public function createEdgeCloneInverted($originalEdge){
        return $this->createEdgeCloneInternal($originalEdge,1,0);
    }
    
    /**
     * create new clone of the given edge between adjacent vertices
     *
     * @param Edge $originalEdge original edge from old graph
     * @param int  $ia           index of start vertex
     * @param int  $ib           index of end vertex
     * @return Edge new edge in this graph
     * @uses Edge::getVerticesId()
     * @uses Graph::getVertex()
     * @uses Vertex::createEdge() to create a new undirected edge if given edge was undrected
     * @uses Vertex::createEdgeTo() to create a new directed edge if given edge was directed
     * @uses Edge::getWeight()
     * @uses Edge::setWeight()
     * @uses Edge::getFlow()
     * @uses Edge::setFlow()
     * @uses Edge::getCapacity()
     * @uses Edge::setCapacity()
     */
    private function createEdgeCloneInternal($originalEdge,$ia,$ib){
        $ends = $originalEdge->getVerticesId();
    
        $a = $this->getVertex($ends[$ia]); // get start vertex from old start vertex id
        $b = $this->getVertex($ends[$ib]); // get target vertex from old target vertex id
    
        if($originalEdge instanceof EdgeDirected){
            $newEdge = $a->createEdgeTo($b);
        }else{
            $newEdge = $a->createEdge($b); // create new edge between new a and b
        }
        // TODO: copy edge attributes
        $newEdge->setLayoutRaw($originalEdge->getLayout());
        $newEdge->setWeight($originalEdge->getWeight());
        $newEdge->setFlow($originalEdge->getFlow());
        $newEdge->setCapacity($originalEdge->getCapacity());
    
        return $newEdge;
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
     * @throws OutOfBoundsException if given vertex ID does not exist
     */
    public function getVertex($id){
        if( ! isset($this->vertices[$id]) ){
            throw new Exception\OutOfBoundsException('Vertex '.$id.' does not exist');
        }
        
        return $this->vertices[$id];
    }
    
    /**
     * return first vertex found
     * 
     * some algorithms do not need a particular vertex, but merely a (random)
     * starting point. this is a convenience function to just pick the first
     * vertex from the list of known vertices.
     *
     * @return Vertex first vertex found in this graph
     * @throws UnderflowException if Graph has no vertices
     * @see Vertex::getFirst() if you need to apply ordering first
     */
    public function getVertexFirst(){
        foreach ($this->vertices as $vertex){
            return $vertex;
        }
        
        throw new Exception\UnderflowException("Graph has no vertices");
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
     * return number of vertices (aka. size of graph, |V| or just 'n')
     * 
     * @return int
     */
    public function getNumberOfVertices(){
        return count($this->vertices);
    }
    
    /**
     * return number of edges
     * 
     * @return int
     */
    public function getNumberOfEdges(){
        return count($this->edges);
    }
    
    /**
     * get degree for k-regular-graph (only if each vertex has the same degree)
     * 
     * @return int
     * @throws UnderflowException if graph is empty
     * @throws RuntimeException if graph is not regular (i.e. vertex degrees are not equal)
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     */
    public function getDegree(){
        $degree = $this->getVertexFirst()->getDegreeIn(); // get initial degree of any start vertex to compare others to
        
        foreach($this->vertices as $vertex){
            $i = $vertex->getDegreeIn();
            
            if($i !== $degree || $i !== $vertex->getDegreeOut()){ // degree same (and for digraphs: indegree=outdegree)
                throw new Exception\RuntimeException('Graph is not k-regular');
            }
        }
        
        return $degree;
    }
    
    /**
     * get minimum degree of vertices
     *
     * @return int
     * @throws Exception if graph is empty or directed
     * @uses Vertex::getFirst()
     * @uses Vertex::getDegree()
     */
    public function getDegreeMin(){
        return Vertex::getFirst($this->vertices,Vertex::ORDER_DEGREE)->getDegree();
    }
    
    /**
     * get maximum degree of vertices
     *
     * @return int
     * @throws Exception if graph is empty or directed
     * @uses Vertex::getFirst()
     * @uses Vertex::getDegree()
     */
    public function getDegreeMax(){
        return Vertex::getFirst($this->vertices,Vertex::ORDER_DEGREE,true)->getDegree();
    }
    
    /**
     * checks whether this graph is regular, i.e. each vertex has the same indegree/outdegree
     * 
     * @return boolean
     * @uses Graph::getDegree()
     */
    public function isRegular(){
        if(!$this->vertices){ // an empty graph is considered regular
            return true;
        }
        try{
            $this->getDegree();
            return true;
        }
        catch(RuntimeException $ignore){ }
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
     * checks whether this graph is symmetric (for every edge a->b there's also an edge b->a)
     * 
     * @return boolean
     * @uses EdgeDirected::getVertexStart()
     * @uses EdgeDirected::getVertedEnd()
     * @uses Vertex::hasEdgeTo()
     */
    public function isSymmetric(){
        foreach($this->edges as $edge){ // check all edges
            if($edge instanceof EdgeDirected){ // only check directed edges (undirected ones are symmetric by definition)
                if(!$edge->getVertexEnd()->hasEdgeTo($edge->getVertexStart())){ // check if end also has an edge to start
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * checks whether this graph has any parallel edges (aka multigraph)
     * 
     * @return boolean
     * @uses Edge::hasEdgeParallel() for every edge
     */
    public function hasEdgeParallel(){
        foreach($this->edges as $edge){
            if($edge->hasEdgeParallel()){
                return true;
            }
        }
        return false;
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
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     */
    public function isBalanced(){
        foreach($this->vertices as $vertex){
            if($vertex->getDegreeIn() !== $vertex->getDegreeOut()){
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
            if($edge instanceof EdgeDirected){
                return true;
            }
        }
        return false;
    }
        
    /**
     * checks whether this graph has any weighted edges
     * 
     * edges usually have no weight attached. a weight explicitly set to (int)0
     * will be considered as 'weighted'.
     * 
     * @return boolean
     * @uses Edge::getWeight()
     */
    public function isWeighted(){
        foreach($this->edges as $edge){
            if($edge->getWeight() !== NULL){
                return true;
            }
        }
        return false;
    }
    
    /**
     * get total weight of graph (sum of weight of all edges)
     * 
     * edges with no weight assigned will evaluate to weight (int)0. thus an
     * unweighted graph (see isWeighted()) will return total weight of (int)0.
     * 
     * returned weight can also be negative or (int)0 if edges have been
     * assigned a negative weight or a weight of (int)0.
     * 
     * @return float total weight
     * @see Graph::isWeighted()
     * @uses Edge::getWeight()
     */
    public function getWeight(){
        $weight = 0;
        foreach($this->edges as $edge){
            $w = $edge->getWeight();
            if($w !== NULL){
                $weight += $w;
            }
        }
        return $weight;
    }
    
    public function getBalance(){
        $balance = 0;
        foreach ($this->getVertices() as $vertex) {                                //Sum for all vertices of value
            $balance += $vertex->getBalance();
        }
        return $balance;
    }
    
    /**
     * get minimum weight assigned to all edges
     * 
     * minimum weight is often needed because some algorithms do not support
     * negative weights or edges with zero weight.
     * 
     * @return float|NULL minimum edge weight or NULL if graph is not weighted or empty
     */
    public function getWeightMin(){
        $min = NULL;
        foreach($this->edges as $edge){
            $weight = $edge->getWeight();
            if($min === NULL || $weight < $min){
                $min = $weight;
            }
        }
        
        return $min;
    }
    
    /**
     * check if this graph has any flow set (any edge has a non-NULL flow)
     *
     * @return boolean
     * @uses Edge::getFlow()
     */
    public function hasFlow(){
    	foreach($this->edges as $edge){
    		if($edge->getFlow() !== NULL){
    			return true;
    		}
    	}
    	return false;
    }
    
    /**
     * check if the current flow is balanced (aka "balanced flow" or "b-flow")
     * 
     * a flow is considered balanced if each edge's current flow does not exceed its
     * maximum capacity (which is always guaranteed due to the implementation
     * of Edge::setFlow()) and each vertices' flow (i.e. inflow-outflow) equals
     * its balance.
     * 
     * checking whether the flow is balanced is not to be confused with checking
     * whether the graph is balanced (see Graph::isBalanced() instead) 
     * 
     * @return boolean
     * @see Graph::isBalanced() if you merely want to check indegree=outdegree
     * @uses Vertex::getFlow()
     * @uses Vertex::getBalance()
     */
    public function isBalancedFlow(){
        // no need to check for each edge: flow <= capacity (setters already check that)
        // check for each vertex: inflow-outflow = balance
        foreach($this->vertices as $vertex){
            if($vertex->getFlow() === $vertex->getBalance()){
                return false;
            }
        }
        return true;
    }
    
    /**
     * get total weight of current flow (sum of all edges flow(e) * weight(e))
     * 
     * @return float
     * @see Graph::getWeight() to just get the sum of all edges' weights
     * @uses Edge::getFlow()
     * @uses Edge::getWeight()
     */
    public function getWeightFlow(){
        $sum = 0;
        foreach($this->edges as $edge){
            $sum += $edge->getFlow() * $edge->getWeight();
        }
        return $sum;
    }
    
    /**
     * checks whether this graph has any loops (edges from vertex to itself)
     * 
     * @return boolean
     * @uses Edge::isLoop()
     */
    public function hasLoop(){
        foreach($this->edges as $edge){
            if($edge->isLoop()){
                return true;
            }
        }
        return false;
    }
    
    /**
     * adds a new Edge to the Graph (MUST NOT be called manually!)
     *
     * @param Edge $edge instance of the new Edge
     * @return void
     * @private
     * @see Vertex::createEdge() instead!
     */
    public function addEdge($edge){
        $this->edges []= $edge;
    }
    
    /**
     * remove the given edge from list of connected edges (MUST NOT be called manually!)
     *
     * @param Edge $edge
     * @return void
     * @throws InvalidArgumentException if given edge does not exist (should not ever happen)
     * @private
     * @see Edge::destroy() instead!
     */
    public function removeEdge($edge){
        $id = array_search($edge,$this->edges,true);
        if($id === false){
            throw new Exception\InvalidArgumentException('Given edge does NOT exist');
        }
        unset($this->edges[$id]);
    }
    
    /**
     * remove the given vertex from list of known vertices (MUST NOT be called manually!)
     *
     * @param Vertex $vertex
     * @return void
     * @throws InvalidArgumentException if given vertex does not exist (should not ever happen)
     * @private
     * @see Vertex::destroy() instead!
     */
    public function removeVertex($vertex){
        $id = array_search($vertex,$this->vertices,true);
        if($id === false){
            throw new Exception\InvalidArgumentException('Given vertex does NOT exist');
        }
        unset($this->vertices[$id]);
    }
    
    /**
     * returns an array of ALL Edges in this graph
     *
     * @return array[Edge]
     */
    public function getEdges(){
        return $this->edges;
    }
    
    /**
     * Extracts (optional: inversed) edge from this graph
     *
     * @param Edge $edge
     * @param boolean $inverse
     * @return Edge
     * @throws UnderflowException if no edge was found
     * @thrwos OverflowException if multiple edges match
     */
    public function getEdgeClone($edge, $inverse=false){
    	// Extract endpoints from edge
    	$originalStartVertexArray = $edge->getVerticesStart();
    	$originalStartVertex = array_shift($originalStartVertexArray);
    
    	$originalTargetVertexArray = $edge->getVerticesTarget();
    	$originalTargetVertex = array_shift($originalTargetVertexArray);
    
    	// swap them if inverse wanted
    	if($inverse){
    		$temp = $originalStartVertex;
    		$originalStartVertex = $originalTargetVertex;
    		$originalTargetVertex = $temp;
    	}
    
    	// Get original vertices from resultgraph
    	$residualGraphEdgeStartVertex = $this->getVertex($originalStartVertex->getId());
    	$residualGraphEdgeTargetVertex = $this->getVertex($originalTargetVertex->getId());
    
    	// Now get the edge
    	$residualEdgeArray = $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);
    
    	// Check for parallel edges
    	if(!$residualEdgeArray){
    	    throw new Exception\UnderflowException('No original edges for given cloned edge found');
    	}else if(count($residualEdgeArray) !== 1){
    		throw new Exception\OverflowException('More than one cloned edge? Parallel edges (multigraph) not supported');
    	}
    
    	return $residualEdgeArray[0];
    }
    
    /**
     * @return int number of components of this graph
     * @uses AlgorithmConnectedComponents::getNumberOfComponents()
     */
    public function getNumberOfComponents(){
        $alg = new AlgorithmConnectedComponents($this);
        return $alg->getNumberOfComponents();
    }
    
    /**
     * count total number of different groups assigned to vertices
     * 
     * @return int
     * @uses AlgorithmGroups::getNumberOfGroups()
     */
    public function getNumberOfGroups(){
        $alg = new AlgorithmGroups($this);
        return $alg->getNumberOfGroups();
    }
    
    public function isBipartit(){
        $alg = new AlgorithmBipartit($this);
        return $alg->isBipartit();
    }
    
    /**
     * do NOT allow cloning of objects (MUST NOT be called!)
     *
     * @throws BadMethodCallException
     * @see Graph::createGraphClone() instead
     */
    private function __clone(){
        throw new Exception\BadMethodCallException();
    }
}
