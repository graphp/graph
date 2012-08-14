<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Edge\Base as Edge;

class Cycle extends Walk{    
    /**
     * create new cycle instance from given predecessor map
     * 
     * @param array[Vertex] $predecessors map of vid => predecessor vertex instance
     * @param Vertex        $vertex       start vertex to search predecessors from
     * @param int           $by
     * @param boolean       $desc
     * @return Cycle
     * @throws Exception\UnderflowException
     * @see Edge::getFirst() for parameters $by and $desc
     * @uses Cycle::factoryFromVertices()
     */
    public static function factoryFromPredecessorMap($predecessors,$vertex,$by=Edge::ORDER_FIFO,$desc=false){
        /*$checked = array();
        foreach($predecessors as $vertex){
            $vid = $vertex->getId();
            if(!isset($checked[$vid])){
                
            }
        }*/
        
        //find a vertex in the cycle
        $vid = $vertex->getId();
        $startVertices = array();
        do{
        	$startVertices[$vid] = $vertex;
        
        	$vertex = $predecessors[$vid];
        	$vid = $vertex->getId();
        }while(!isset($startVertices[$vid]));
        
        //find negative cycle
        $vid = $vertex->getId();
        $vertices = array();                                                   // build array of vertices in cycle
        do{
        	$vertices[$vid] = $vertex;                                          // add new vertex to cycle
        
        	$vertex = $predecessors[$vid];                                      // get predecessor of vertex
        	$vid = $vertex->getId();
        }while(!isset($vertices[$vid]));                                      // continue until we find a vertex that's already in the circle (i.e. circle is closed)
        
        $vertices = array_reverse($vertices,true);                             // reverse cycle, because cycle is actually built in opposite direction due to checking predecessors
        
        return Cycle::factoryFromVertices($vertices,$by,$desc);
    }
    
    /**
     * create new cycle instance with edges between given vertices
     * 
     * @param array[Vertex] $vertices
     * @param int           $by
     * @param boolean       $desc
     * @return Cycle
     * @throws Exception\UnderflowException if no vertices were given
     * @see Edge::getFirst() for parameters $by and $desc
     */
    public static function factoryFromVertices($vertices,$by=Edge::ORDER_FIFO,$desc=false){
        $edges = array();
        $first = NULL;
        $last = NULL;
        foreach($vertices as $vertex){
        	if($first === NULL){    // skip first vertex as last is unknown
        		$first = $vertex;
        	}else{
        		$edges []= Edge::getFirst($last->getEdgesTo($vertex),$by,$desc); // pick edge between last vertex and this vertex
        	}
        	$last = $vertex;
        }
        if($last === NULL){
            throw new Exception\UnderflowException('No vertices given');
        }
        $edges []= Edge::getFirst($last->getEdgesTo($first),$by,$desc);         // additional edge from last vertex to first vertex
        
        return new Cycle($vertices,$edges);
    }
    
    /**
     * create new cycle instance with vertices connected by given edges
     * 
     * @param array[Edge] $edges
     * @param Vertex      $startVertex
     * @return Cycle
     */
    public static function factoryFromEdges(array $edges,Vertex $startVertex){
        $vertices = array($startVertex->getId() => $startVertex);
        foreach($edges as $edge){
            $vertex = $edge->getVertexToFrom($startVertex);
            $vertices[$vertex->getId()] = $vertex;
            $startVertex = $vertex;
        }
        
        return new Cycle($vertices,$edges);
    }
}
