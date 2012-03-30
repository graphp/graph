<?php

class GraphViz{
	public function __construct(Graph $graphToPlot){
		$this->graph = $graphToPlot;
	}

	/**
	 * @return GraphViz script
	 */
	public function createGraphVizScript(){
		$script = "digraph G {\n";
		foreach ($this->graph->getVertices() as $vertice){
			foreach ($vertice->getVerticesEdgeTo() as $verticeTo){
				$script .= $vertice->getId()." -> ".$verticeTo->getId().";\n";
			}
		}
		$script .= "\n}";
			
		return $script;
	}
}

?>