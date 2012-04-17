<?php

class GraphViz{
	public function __construct(Graph $graphToPlot){
		$this->graph = $graphToPlot;
	}

	/**
	 * @return GraphViz script with one edge between every vertex
	 */
	public function createUndirectedGraphVizScript(){
		$script = "graph G {\n";
		$mark = array();
			
		foreach ($this->graph->getVertices() as $vertice){
			foreach ($vertice->getEdges() as $currentEdge){
				foreach ($currentEdge->getTargetVertices() as $currentTargetVertice) {
						
					if($currentTargetVertice != $vertice && !isset($mark[$currentTargetVertice->getId()])){
						$script .= $vertice->getId()." -- ".$currentTargetVertice->getId()." [label=".$currentEdge->value."];\n";
					}
						
				}
			}
			$mark[$vertice->getId()] = true;
		}
		$script .= "\n}";
			
		return $script;
	}

	/**
	 * @return GraphViz script with all given edges
	 */
	public function createDirectedGraphVizScript(){
		$script = "digraph G {\n";
		$mark = array();

		foreach ($this->graph->getVertices() as $vertice){
			foreach ($vertice->getEdges() as $currentEdge){
				foreach ($currentEdge->getTargetVertices() as $currentTargetVertice) {
						
					if($currentTargetVertice != $vertice && !isset($mark[$currentTargetVertice->getId()])){
						$script .= $vertice->getId()." -> ".$currentTargetVertice->getId()." [label=".$currentEdge->value."];\n";
					}
						
				}
			}
		}
		$script .= "\n}";

		return $script;
	}
}