<?php

class AlgorithmSpBreadthFirst extends AlgorithmSp{
    /**
     * start vertex this algorithm operates on
     * 
     * @var Vertex
     */
    private $vertex;
    
    private $inverse;
    
    public function __construct($startVertex,$inverse=false){
        $this->vertex = $startVertex;
        $this->inverse = $inverse;
    }
    
    /**
     * get distance between start vertex and given end vertex
     * 
     * @param Vertex $endVertex
     * @throws Exception if there's no path to given end vertex
     * @return int
     * @uses AlgorithmSpBreadthFirst::getDistanceMap()
     */
    public function getDistance($endVertex){
        $vid = $endVertex->getId();
        $map = $this->getDistanceMap();
        if(!isset($map[$vid])){
            throw new Exception();
        }
        return $map[$vid];
    }
    
    /**
     * get map of vertex IDs to distance
     * 
     * @return array[int]
     * @uses Vertex::hasLoop()
     */
    public function getDistanceMap(){
        $map = array();
        if($this->vertex->hasLoop()){
            $map[$this->vertex->getId()] = 1;
        }
        
        $vertex = $this->vertex;
        // TODO: actual breadth search + remember level
        $nexts = $this->inverse ? $vertex->getVerticesEdgeFrom() : $vertex->getVerticesEdgeTo();
        
        return $map;
    }
    
    /**
     * checks whether there's a path from this start vertex to given end vertex
     * 
     * @param Vertex $endVertex
     * @return boolean
     * @uses AlgorithmSpBreadthFirst::getDistanceMap()
     */
    public function hasVertex($endVertex){
        $map = $this->getDistanceMap();
        return isset($map[$endVertex->getId()]);
    }
    
    /**
     * get array of all target vertices this vertex has a path to
     * 
     * @return array[Vertex]
     * @uses AlgorithmSpBreadthFirst::getDistanceMap()
     */
    public function getVertices(){
        $ret = array();
        $graph = $this->vertex->getGraph();
        foreach($this->getDistanceMap() as $vid=>$unusedDistance){
            $ret[$vid] = $graph->getVertex($vid);
        }
        return $ret;
    }
}
