<?php

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

	//     public function getVerticesFrom(){
	//         return array($this->a,$this->b);
	//     }

	//     public function getVerticesTo(){
	//         return array($this->a,$this->b);
	//     }

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

	//     public function hasVertexFrom($vertex){
	//         return ($this->a === $vertex || $this->b === $vertex);
	//     }

	//     public function hasVertexTo($vertex){
	//         return ($this->a === $vertex || $this->b === $vertex);
	//     }

	public function isLoop(){
	    return ($this->a === $this->b);
	}
	
	public function isConnection($from, $to){
		//							  one way				or						other way
		return ( ( $this->a === $from->getId() && $this->b === $to->getId() ) || ( $this->b === $from->getId() && $this->a === $to->getId() ) );
	}

	public function getVertexToFrom($startVertex){
		if ($this->a === $startVertex->getId()){
			return $this->graph->getVertex($this->b);
		}
		else if($this->b === $startVertex->getId()){
			return $this->graph->getVertex($this->a);
		}
		else{
			throw new Exception('Invalid start vertex');
		}
	}

	public function getVertexFromTo($endVertex){
		if ($this->a === $endVertex->getId()){
			return $this->graph->getVertex($this->b);
		}
		else if($this->b === $endVertex->getId()){
			return $this->graph->getVertex($this->a);
		}
		else{
			throw new Exception('Invalid end vertex');
		}
	}
	
	public function getVertexFromToById($endVertex){
		if ($this->a == $endVertex->getId()){
			return $this->graph->getVertex($this->b);
		}
		if ($this->b == $endVertex->getId()){
			return $this->graph->getVertex($this->a);
		}
		else{
			throw new Exception('Invalid end vertex');
		}
	}
	
	public function hasVertexStart(Vertex $startVertex){
		return ($this->graph->getVertex($this->a) === $startVertex || $this->graph->getVertex($this->b) === $startVertex);
	}
}
