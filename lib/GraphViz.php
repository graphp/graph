<?php

class GraphViz{
	public function __construct(Graph $graphToPlot){
		$this->graph = $graphToPlot;
	}
	
	public function display(){
        $script = $this->createUnDirectedGraphVizScript();
        
        var_dump($script);
        
        $tmp = tempnam('/tmp','graphviz');
        file_put_contents($tmp,$script);
        
        
        echo "Generate picture ...";
        
        exec('dot -Tpng '.$tmp.' -o '.$tmp.'.png');
        exec('xdg-open '.$tmp.'.png # > /dev/null &2>1 &');
        
        echo "... done\n";
	}

	/**
	 * @return GraphViz script with one edge between every vertex
	 */
	public function createUndirectedGraphVizScript(){
		$script = "graph G {\n";
		$mark = array();
			
		foreach ($this->graph->getVertices() as $vertex){
			foreach ($vertex->getEdges() as $currentEdge){
			    $currentTargetVertex = $currentEdge->getVertexToFrom($vertex);
				
				if($currentTargetVertex !== $vertex && !isset($mark[$currentTargetVertex->getId()])){
					$script .= $vertex->getId()." -- ".$currentTargetVertex->getId();
					
					$weight = $currentEdge->getWeight();
					if($weight !== NULL){                                       // add weight as label (if set)
					    $script .= " [label=".$weight."]";
					}
					$script .= ";\n";
				}
			}
			$mark[$vertex->getId()] = true;
		}
		$script .= "\n}";
			
		return $script;
	}

	/**
	 * @return GraphViz script with all given edges
	 */
	public function createDirectedGraphVizScript(){
		$script = "digraph G {\n";
		
		foreach ($this->graph->getVertices() as $vertex){
			foreach ($vertex->getEdges() as $currentEdge){
			    $currentTargetVertex = $currentEdge->getVertexToFrom($vertex);
						
				if($currentTargetVertex !== $vertex){
					$script .= $vertex->getId()." -> ".$currentTargetVertex->getId();
					
					$weight = $currentEdge->getWeight();
					if($weight !== NULL){                                       // add weight as label (if set)
					    $script .= " [label=".$weight."]";
					}
					$script .= "\n";
				}
			}
		}
		$script .= "\n}";

		return $script;
	}
}