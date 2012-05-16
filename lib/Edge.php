<?php

abstract class Edge{
    /**
     * do not change order - FIFO : first in, first out
     * 
     * @var int
     */
    const ORDER_FIFO = 0;
    
    /**
     * order by edge weight
     * 
     * @var int
     * @see Edge::setWeight()
     */
    const ORDER_WEIGHT = 1;
    
    /**
     * random/shuffled order
     *
     * @var int
     */
    const ORDER_RANDOM = 5;
    
    /**
     * get first edge (optionally ordered by given criterium $by) from given array of edges
     *
     * @param array[Edge]|Graph $edges array of edges to scan for 'first' edge
     * @param int               $by       criterium to sort by. see Edge::ORDER_WEIGHT, etc.
     * @param boolean           $desc     whether to return biggest (true) instead of smallest (default:false)
     * @return Edge
     * @throws Exception if criterium is unknown or no edges exist
     * @uses Edge::getWeight()
     */
    public static function getFirst($edges,$by=self::ORDER_FIFO,$desc=false){
        if($edges instanceof Graph){
            $edges = $edges->getEdges();
        }
        if($by === self::ORDER_RANDOM && $edges){ // random order and there are actually some edges to shuffle
            return $edges[array_rand($edges)]; // just return by random key (no need to check for DESC flag)
        }
        $ret = NULL;
        $best = NULL;
        foreach($edges as $edge){
            if($by === self::ORDER_FIFO){        // do not sort - needs special handling
                if($desc){            // always remember edge from last iteration
                    $ret = $edge;
                    continue;
                }else{                // just return first edge right away
                    return $edge;
                }
            }else if($by === self::ORDER_WEIGHT){
                $now = $edge->getWeight();
            }else{
                throw new Exception('Invalid order flag "'.$by.'"');
            }
            if($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)){
                $ret = $edge;
                $best = $now;
            }
        }
        if($ret === NULL){
            throw new Exception('No edge found');
        }
        return $ret;
    }
    
    /**
     * get all edges ordered by given criterium $by
     * 
     * @param array[Edge]|Graph $edges array of edges to sort
     * @param int               $by    criterium to sort by. see Edge::ORDER_WEIGHT, etc.
     * @param boolean           $desc  whether to return biggest (true) instead of smallest (default:false)
     * @return array
     * @throws Exception if criterium is unknown
     * @uses Edge::getWeight()
     * @todo return Iterator and use SplPriorityQueue instead of temporary array
     * @link http://matthewturland.com/2010/05/20/new-spl-features-in-php-5-3/
     */
    public static function getAll($edges,$by=self::ORDER_FIFO,$desc=false){
        if($edges instanceof Graph){
            $edges = $edges->getEdges();
        }
        if($by === self::ORDER_RANDOM){
            shuffle($edges);
            return new ArrayIterator($edges); // create iterator for shuffled array (no need to check DESC flag)
        }
        if($by === self::ORDER_FIFO){
            return new ArrayIterator($desc ? array_reverse($edges) : $edges);
        }
        $temp = array(); // temporary indexed array to be sorted
        foreach($edges as $eid=>$edge){
            if($by === self::ORDER_WEIGHT){
                $now = $edge->getWeight();
            }else{
                throw new Exception('Invalid sort criterium');
            }
            $temp[$eid] = $now;
        }
        if($desc){ // actually sort array ASC/DESC
            arsort($temp);
        }else{
            asort($temp);
        }
        foreach($temp as $eid=>&$value){ // make sure resulting array is edigeId=>edge
            $value = $edges[$eid];
        }
        return $temp;
    }
    
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
	 * get Vertices that are the start of this edge
	 *
	 * @return array[Vertex]
	 */
	abstract public function getStartVertices();
	
	/**
	 * return true if this edge is an outgoing edge of the given vertex
	 * return false ; every other case
	 *
	 * @return boolean
	 */
	abstract public function isOutgoingEdgeOf(Vertex $startVertex);
	
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
	
	private $layout = array();
	
	public function getLayout(){
		return $this->layout;
	}
	
	public function setLayoutRaw($name,$value){
		if($value === NULL){
			unset($this->layout[$name]);
		}else{
			$this->layout[$name] = $value;
		}
		return $this;
	}
	
	public function setLayout($name,$value){
		return $this->setLayoutRaw($name,GraphViz::escape($value));
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
