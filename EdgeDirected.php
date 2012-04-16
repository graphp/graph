<?php

class EdgeDirected extends Edge{
	private $from;
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

	//     public function getVerticesFrom(){
	//         return array($this->from);
	//     }

	//     public function getVerticesTo(){
	//         return array($this->to);
	//     }
	
	public function getTargetVertices(){
		return array($this->to);
	}

	//     public function hasVertexFrom($vertex){
	//         return ($this->from === $vertex);
	//     }

	//     public function hasVertexTo($vertex){
	//         return ($this->to === $vertex);
	//     }

	public function isConnection($from, $to){
		return ($this->to === $to && $this->from === $from);
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
}
