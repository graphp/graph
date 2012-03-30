<?php

class AlgorithmSearchDepthFirst{
	
	/**
	 * Start vertex for this algorithm
	 * 
	 * @var Vertex
	 */
	private $StartVertex = NULL;
	
	/**
	 * 
	 * @param Vertex $startVertex
	 */
	public function __construct($startVertex){
		$this->StartVertex = $startVertex;
	}
	
	/**
	 * 
	 * calculates the recursive algorithm
	 * 
	 * fills $this->visitedVertices
	 * 
	 * @param Vertex $vertex
	 */
	private function recursiveDepthFirstSearch($vertex, & $visitedVertices){
		
		if ( ! isset($visitedVertices[$vertex->getId()]) ){						//	If I didn't visited this vertex before
			$visitedVertices[$vertex->getId()] = $vertex;						//		Add Vertex to already visited vertices
			
			$nextVertices = $vertex->getVerticesEdgeTo();						//		Get next vertices
			
			foreach ($nextVertices as $nextVertix){
				$this->recursiveDepthFirstSearch($nextVertix, $visitedVertices);//			recursive call for next vertices
			}
		}
	}

	/**
	 * 
	 * calculates a recursive depth-first search
	 * 
	 * @return array[Vertex]
	 */
	public function getVertices(){
		$visitedVertices = array();
		$this->recursiveDepthFirstSearch($this->StartVertex, $visitedVertices);
		return $visitedVertices;
	}
}
