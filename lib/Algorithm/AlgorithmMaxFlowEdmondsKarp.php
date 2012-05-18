<?php

class AlgorithmMaxFlowEdmondsKarp extends Algorithm{
	
	/**
	 * 
	 * @var Graph
	 */
	private $graph;
	
	/**
	 * @var Vertex
	 */
	private $startVertex;
	
	/**
	 * @var Vertex
	 */
	private $destinationVertex;
	
	/**
	 * 
	 * @param Vertex $startVertex the vertex where the flow search starts
	 * @param Vertex $destinationVertex the vertex where the flow search ends the destination
     */
	public function __construct(Vertex $startVertex, Vertex $destinationVertex){
		$this->startVertex = $startVertex;
		$this->destinationVertex = $destinationVertex;
		$this->graph = $startVertex->getGraph();
	}
	
	private function start(){
	    
	    $currentGraph = $this->mergeParallelEdges($this->graph);                // remove parallel edges
	    
	    do{
	        
	        $path=$this->getGraphShortestPath($currentGraph);                   // Get Shortes path if NULL-> Done
	        if($path){
	            $currentGraph=$this->getResidualGraph($currentGraph, $path);
	        }
	    }
	    while($path);
	       return $this->getFlowGraphFromResidualGraph($currentGraph);
	}
	
	private function getFlowGraphFromResidualGraph($residualGraph){
	    //TODO generate flow $returnGraph from $residualGraph and $this->graph
    
	    return new Graph();
	}
	
	private function mergeParallelEdges($currentGraph){
	    //TODO 1. find and merge parallel edges
	    return $currentGraph;
	}
	
	private function getGraphShortestPath($currentGraph)
	{
	    // TODO 
	    // 1. Search shortest path from s -> t
	    $breadthSearchAlg = new AlgorithmSearchBreadthFirst($this->startVertex);
	    $path = $breadthSearchAlg->getGraphPathTo($this->destinationVertex);
	    
	    // 2. get max flow from path
	    // 3. create graph with shortest path and max flow as edge values
	    return new graph;
	}
	
	private function getResidualGraph($currentGraph, $path)
	{
	    //TODO 
		// 1. Substract $path values from $graph
		// 2. add in reversed direction of $path values to the $graph
		return new graph();
	}
	
	/**
	 * returns max flow graph
	 *
	 * @return Graph
	 */
	public function getResultGraph(){
	   
	    return $this->start();	
	}
}
