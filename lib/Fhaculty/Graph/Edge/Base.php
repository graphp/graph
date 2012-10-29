<?php

namespace Fhaculty\Graph\Edge;

use Fhaculty\Graph\Layoutable;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Exception\LogicException;
use Fhaculty\Graph\Exception\RangeException;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\BadMethodCallException;
use \ArrayIterator;

abstract class Base extends Layoutable{
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
     * @see Edge::getWeight()
     */
    const ORDER_WEIGHT = 1;

    /**
     * order by edge capacity
     *
     * @var int
     * @see Edge::getCapacity()
     */
    const ORDER_CAPACITY = 2;
    
    /**
     * order by remaining capacity on edge (maximum capacity - current flow)
     * 
     * @var int
     * @see Edge::getCapacityRemaining()
     */
    const ORDER_CAPACITY_REMAINING = 3;
    
    /**
     * order by edge flow
     *
     * @var int
     * @see Edge::getFlow()
     */
    const ORDER_FLOW = 4;
    
    /**
     * random/shuffled order
     *
     * @var int
     */
    const ORDER_RANDOM = 5;

    /**
     * get first edge (optionally ordered by given criterium $by) from given array of edges
     *
     * @param Edge[]|Graph $edges array of edges to scan for 'first' edge
     * @param int               $by       criterium to sort by. see Edge::ORDER_WEIGHT, etc.
     * @param boolean           $desc     whether to return biggest (true) instead of smallest (default:false)
     * @return Edge
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnderflowException if no edges exist
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
            }else if($by === self::ORDER_CAPACITY){
                $now = $edge->getCapacity();
            }else if($by === self::ORDER_CAPACITY_REMAINING){
                $now = $edge->getCapacityRemaining();
            }else if($by === self::ORDER_FLOW){
                $now = $edge->getFlow();
            }else{
                throw new InvalidArgumentException('Invalid order flag "'.$by.'"');
            }
            if($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)){
                $ret = $edge;
                $best = $now;
            }
        }
        if($ret === NULL){
            throw new UnderflowException('No edge found');
        }
        return $ret;
    }

    /**
     * get all edges ordered by given criterium $by
     *
     * @param Edge[]|Graph $edges array of edges to sort
     * @param int               $by    criterium to sort by. see Edge::ORDER_WEIGHT, etc.
     * @param boolean           $desc  whether to return biggest (true) instead of smallest (default:false)
     * @return array
     * @throws DomainException if criterium is unknown
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
            return $edges; // create iterator for shuffled array (no need to check DESC flag)
        }
        if($by === self::ORDER_FIFO){
            return $desc ? array_reverse($edges) : $edges;
        }
        $temp = array(); // temporary indexed array to be sorted
        foreach($edges as $eid=>$edge){
            if($by === self::ORDER_WEIGHT){
                $now = $edge->getWeight();
            }else if($by === self::ORDER_CAPACITY){
            	$now = $edge->getCapacity();
            }else if($by === self::ORDER_CAPACITY_REMAINING){
            	$now = $edge->getCapacityRemaining();
            }else if($by === self::ORDER_FLOW){
            	$now = $edge->getFlow();
            }else{
                throw new InvalidArgumentException('Invalid sort criterium');
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
     * maximum capacity (maximum flow)
     *
     * @var float|int|NULL
     * @see Edge::getCapacity()
     */
    protected $capacity = NULL;

    /**
     * flow (capacity currently in use)
     *
     * @var float|int|NULL
     * @see Edge::getFlow()
     */
    protected $flow = NULL;

    /**
     * get Vertices that are a target of this edge
     *
     * @return Vertex[]
     */
    abstract public function getVerticesTarget();

    /**
     * get Vertices that are the start of this edge
     *
     * @return Vertex[]
     */
    abstract public function getVerticesStart();

    /**
     * return true if this edge is an outgoing edge of the given vertex (i.e. the given vertex is a valid start vertex of this edge)
     * 
     * @param Vertex $startVertex
     * @return boolean
     * @uses Vertex::getVertexToFrom()
     */
    public function hasVertexStart(Vertex $startVertex){
        try{
            $this->getVertexToFrom($startVertex);
            return true;
        }
        catch(InvalidArgumentException $ignore){ }
        return false;
    }
    
    /**
     * return true if this edge is an ingoing edge of the given vertex (i.e. the given vertex is a valid end vertex of this edge)
     *
     * @param Vertex $targetVertex
     * @return boolean
     * @uses Vertex::getVertexFromTo()
     */
    public function hasVertexTarget(Vertex $targetVertex){
        try{
            $this->getVertexFromTo($targetVertex);
            return true;
        }
        catch(InvalidArgumentException $ignore){ }
        return false;
    }
    
    abstract public function isConnection(Vertex $from,Vertex $to);

    /**
     * returns whether this edge is actually a loop
     *
     * @return boolean
     */
    abstract public function isLoop();
    
    /**
     * get target vertex we can reach with this edge from the given start vertex
     *
     * @param Vertex $startVertex
     * @return Vertex
     * @throws InvalidArgumentException if given $startVertex is not a valid start
     * @see Edge::hasEdgeFrom() to check if given start is valid
     */
    abstract public function getVertexToFrom(Vertex $startVertex);
    
    /**
     * get start vertex which can reach us(the given end vertex) with this edge
     *
     * @param Vertex $startVertex
     * @return Vertex
     * @throws InvalidArgumentException if given $startVertex is not a valid end
     * @see Edge::hasEdgeFrom() to check if given start is valid
     */
    abstract public function getVertexFromTo(Vertex $endVertex);
    
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
     * @throws DomainException if given weight is not numeric
     */
    public function setWeight($weight){
        if($weight !== NULL && !is_float($weight) && !is_int($weight)){
            throw new InvalidArgumentException('Invalid weight given - must be numeric or NULL');
        }
        $this->weight = $weight;
        return $this;
    }

    /**
     * get total capacity of this edge
     *
     * @return float|int|NULL numeric capacity or NULL=not set
     */
    public function getCapacity(){
        return $this->capacity;
    }

    /**
     * get the capacity remaining (total capacity - current flow)
     * 
     * @return float|int|NULL numeric capacity remaining or NULL=no upper capacity set
     */
    public function getCapacityRemaining(){
        if($this->capacity === NULL){
            return NULL;
        }
        return $this->capacity - $this->flow;
    }
    
    /**
     * set new total capacity of this edge
     *
     * @param float|int|NULL $capacity
     * @return Edge $this (chainable)
     * @throws DomainException if $capacity is invalid (not numeric or negative)
     * @throws InvalidArgumentException if current flow exceeds new capacity
     */
    public function setCapacity($capacity){
        if($capacity !== NULL){
            if(!is_float($capacity) && !is_int($capacity)){
                throw new InvalidArgumentException('Invalid capacity given - must be numeric');
            }
            if($capacity < 0){
                throw new InvalidArgumentException('Capacity must not be negative');
            }
            if($this->flow !== NULL && $this->flow > $capacity){
                throw new InvalidArgumentException('Current flow of '.$this->flow.' exceeds new capacity');
            }
        }
        $this->capacity = $capacity;
        return $this;
    }

    /**
     * get current flow (capacity currently in use)
     *
     * @return float|int|NULL numeric flow or NULL=not set
     */
    public function getFlow(){
        return $this->flow;
    }

    /**
     * set new total flow (capacity currently in use)
     *
     * @param float|int|NULL $flow
     * @return Edge $this (chainable)
     * @throws Exception if $flow is invalid or flow exceeds maximum capacity
     */
    public function setFlow($flow){
        if($flow !== NULL){
            if(!is_float($flow) && !is_int($flow)){
                throw new InvalidArgumentException('Invalid flow given - must be numeric');
            }
            if($flow < 0){
                throw new InvalidArgumentException('Flow must not be negative');
            }
            if($this->capacity !== NULL && $flow > $this->capacity){
                throw new RangeException('New flow exceeds maximum capacity');
            }
        }
        $this->flow = $flow;
        return $this;
    }
    
    /**
     * checks whether this edge has any parallel edges
     *
     * @return boolean
     * @uses Edge::getEdgesParallel()
     */
    public function hasEdgeParallel(){
        return !!$this->getEdgesParallel();
    }

    /**
     * get all edges parallel to this edge (excluding self)
     *
     * @return Edge[]
     * @throws LogicException
     */
    public function getEdgesParallel(){
        $ends = $this->getVertices();
         
        $edges = $ends[0]->getEdgesTo($ends[1]);                            // get all edges between this edge's endpoints
        if($this->isConnection($ends[1],$ends[0])){                         // edge points into both directions (undirected/bidirectional edge)
            $back = $ends[1]->getEdgesTo($ends[0]);                             // also get all edges in other direction
            foreach($back as $edge){
                if(!in_array($edge,$edges)){
                    $edges[] = $edge;
                }
            } // alternative implementation for array_unique(), because it requires casting edges to string
        }
         
        $pos = array_search($this,$edges,true);
        if($pos === false){
            throw new LogicException('Internal error: Current edge not found');
        }
         
        unset($edges[$pos]);                                                   // exclude current edge from parallel edges
        return array_values($edges);
    }

    /**
     * get all vertices this edge connects
     *
     * @return Vertex[]
     */
    abstract public function getVertices();

    /**
     * get IDs of all vertices this edge connects
     *
     * @return int[]
     * @see Edge::getVertices()
     */
    public function getVerticesId(){
        $ret = $this->getVertices();
        foreach($ret as &$v){
            $v = $v->getId();
        }
        return $ret;
    }

    /**
     * get graph instance this edge is attached to
     *
     * @return Graph
     * @throws LogicException
     */
    public function getGraph(){
        foreach($this->getVertices() as $vertex){
            return $vertex->getGraph();
        }
        throw new LogicException('Internal error: should not be reached');
    }

    /**
     * destroy edge and remove reference from vertices and graph
     *
     * @uses Graph::removeEdge()
     * @uses Vertex::removeEdge()
     * @return void
     */
    public function destroy(){
        $this->getGraph()->removeEdge($this);
        foreach($this->getVertices() as $vertex){
            $vertex->removeEdge($this);
        }
    }

    /**
     * create new clone of this edge between adjacent vertices
     *
     * @return Edge new edge
     * @uses Graph::createEdgeClone()
     */
    public function createEdgeClone(){
        return $this->getGraph()->createEdgeClone($this);
    }

    /**
     * create new clone of this edge inverted (in opposite direction) between adjacent vertices
     *
     * @return Edge new edge
     * @uses Graph::createEdgeCloneInverted()
     */
    public function createEdgeCloneInverted(){
        return $this->getGraph()->createEdgeCloneInverted($this);
    }

    /**
     * do NOT allow cloning of objects
     *
     * @throws Exception
     */
    private function __clone(){
        throw new BadMethodCallException();
    }
}
