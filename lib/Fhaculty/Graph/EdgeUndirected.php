<?php

namespace Fhaculty\Graph;

class EdgeUndirected extends Edge{
    /**
     * vertex a
     *
     * @var Vertex
     */
    private $a;
    
    /**
     * vertex b
     *
     * @var Vertex
     */
    private $b;

    public function __construct($a,$b){
        $this->a = $a;
        $this->b = $b;
    }
    
    public function getVerticesTarget(){
        return array($this->b,$this->a);
    }
    
    public function getVerticesStart(){
        return  array($this->a,$this->b);
    }
    
    public function getVertices(){
        return array($this->a,$this->b);
    }
    
    public function toString(){
        return $this->a->getId()." <-> ".$this->b->getId()." Weight: ".$this->weight;
    }
    
    public function isConnection($from, $to){
        //                              one way                or                        other way
        return ( ( $this->a === $from && $this->b === $to ) || ( $this->b === $from && $this->a === $to ) );
    }
    
    public function isLoop(){
        return ($this->a === $this->b);
    }

    public function getVertexToFrom($startVertex){
        if ($this->a === $startVertex){
            return $this->b;
        }
        else if($this->b === $startVertex){
            return $this->a;
        }
        else{
            throw new Exception\InvalidArgumentException('Invalid start vertex');
        }
    }

    public function getVertexFromTo($endVertex){
        if ($this->a === $endVertex){
            return $this->b;
        }
        else if($this->b === $endVertex){
            return $this->a;
        }
        else{
            throw new Exception\InvalidArgumentException('Invalid end vertex');
        }
    }
    
    public function hasVertexStart(Vertex $startVertex){
        return ($this->a === $startVertex || $this->b === $startVertex);
    }
    
    public function hasVertexTarget(Vertex $targetVertex){
        return $this->hasVertexStart($targetVertex); // same implementation as direction does not matter
    }
}
