<?php

namespace Fhaculty\Graph;

class EdgeUndirectedId extends Edge{
    /**
     * vertex ID of point a
     *
     * @var string
     */
    private $a;
    
    /**
     * Vertex ID of point b
     * 
     * @var string
     */
    private $b;
    
    /**
     * parent graph
     * 
     * @var Graph
     */
    private $graph;

    public function __construct($a,$b){
        $this->a = $a->getId();
        $this->b = $b->getId();
        $this->graph = $a->getGraph();
    }

    public function getGraph(){
        return $this->graph;
    }
    
    public function getVerticesStart(){
        return array($this->graph->getVertex($this->a),$this->graph->getVertex($this->b));
    }
    
    public function getVerticesTarget(){
        return array($this->graph->getVertex($this->b),$this->graph->getVertex($this->a));
    }
    
    public function getVertices(){
        return array($this->graph->getVertex($this->a),$this->graph->getVertex($this->b));
    }
    
    public function getVerticesId(){
        return array($this->a,$this->b);
    }
    
    public function toString(){
        return $this->a." <-> ".$this->b." Weight: ".$this->weight;
    }
    
    public function isLoop(){
        return ($this->a === $this->b);
    }
    
    public function isConnection($from, $to){
        if($from->getGraph() !== $this->graph || $to->getGraph() !== $this->graph){
            return false;
        }
        //                              one way                or                        other way
        return ( ( $this->a === $from->getId() && $this->b === $to->getId() ) || ( $this->b === $from->getId() && $this->a === $to->getId() ) );
    }

    public function getVertexToFrom($startVertex){
        if($startVertex->getGraph() === $this->graph){
            if ($this->a === $startVertex->getId()){
                return $this->graph->getVertex($this->b);
            }
            else if($this->b === $startVertex->getId()){
                return $this->graph->getVertex($this->a);
            }
        }
        throw new InvalidArgumentException('Invalid start vertex');
    }

    public function getVertexFromTo($endVertex){
        if($endVertex->getGraph() === $this->graph){
            if ($this->a === $endVertex->getId()){
                return $this->graph->getVertex($this->b);
            }
            else if($this->b === $endVertex->getId()){
                return $this->graph->getVertex($this->a);
            }
        }
        throw new InvalidArgumentException('Invalid end vertex');
    }
    
    public function hasVertexStart(Vertex $startVertex){
        if($startVertex->getGraph() !== $this->graph){
            return false;
        }
        return ($this->graph->getVertex($this->a) === $startVertex || $this->graph->getVertex($this->b) === $startVertex);
    }
    
    public function hasVertexTarget(Vertex $targetVertex){
        return $this->hasVertexStart($targetVertex); // same implementation as direction does not matter
    }
}
