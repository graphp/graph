<?php

class EdgeDirected extends Edge{
    /**
     * source/start vertex
     * 
     * @var Vertex
     */
    private $from;
    
    /**
     * target/end vertex
     * 
     * @var Vertex
     */
    private $to;

    /**
     * creats a new Edge
     *
     * @param Vertex $from start/source Vertex
     * @param Vertex $to   end/target Vertex
     */
    public function __construct($from,$to){
        $this->from = $from;
        $this->to = $to;
    }
    
    public function getVerticesTarget(){
        return array($this->to);
    }
    
    public function getVerticesStart(){
        return array($this->from);
    }
    
    public function getVertices(){
        return array($this->from,$this->to);
    }
    
    /**
     * get end/target vertex
     * 
     * @return Vertex
     */
    public function getVertexEnd(){
        return $this->to;
    }
    
    /**
     * get start vertex
     * 
     * @return Vertex
     */
    public function getVertexStart(){
        return $this->from;
    }
    
    public function toString(){
        return $this->from->getId()." -> ".$this->to->getId()." Weight: ".$this->weight;
    }
    
    public function isConnection($from, $to){
        return ($this->to === $to && $this->from === $from);
    }
    
    public function isLoop(){
        return ($this->to === $this->from);
    }
    
    public function getVertexToFrom($startVertex){
        if ($this->from !== $startVertex){
            throw new Exception('Invalid start vertex');
        }
        return $this->to;
    }

    public function getVertexFromTo($endVertex){
        if ($this->to !== $endVertex){
            throw new Exception('Invalid end vertex');
        }
        return $this->from;
    }
    
    public function hasVertexStart(Vertex $startVertex){
        return ($this->from === $startVertex);
    }
    
    public function hasVertexTarget(Vertex $targetVertex){
        return ($this->to === $targetVertex);
    }
}
