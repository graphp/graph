<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exception\LogicException;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;

/**
 *
 * @author clue
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 */
abstract class Set{
    /**
     *
     * @var array[Edge]
     */
    protected $edges = array();

    /**
     *
     * @var array[Vertex]
     */
    protected $vertices = array();

    /**
     * returns an array of ALL Edges in this graph
     *
     * @return array[Edge]
     */
    public function getEdges(){
        return $this->edges;
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
}
