<?php
class AlgorithmTSPMinimumSpanningTreeHeuristic{
	
	private $graph;
	
	public function __construct(Graph $inputGraph){
 		$this->graph = $inputGraph;
	}
	
	/**
	 *
	 * @param Vertex $startVertex
	 * @return Graph
	 */
	public function getResultGraph(Vertex $startVertex){
		$returnGraph = $this->graph->createGraphCloneEdgeless();
		
		$minimumSpanningTreeAlgorithm = new AlgorithmKruskal($this->graph);
		$minimumSpanningTree = $minimumSpanningTreeAlgorithm->getResultGraph();
		
		$depthFirstSearch = $minimumSpanningTree->getAnyVertex()->searchDepthFirst();
		
		$oldVertex = NULL;
		$startVertex = NULL;
		
		foreach ($depthFirstSearch as $vertex){
			
			if ($startVertex === NULL){
				$startVertex = $vertex;
			}
			else {
				$returnGraph->getVertex( $oldVertex->getId() )->createEdge( $vertex );
			}
			
			$oldVertex = $vertex;
		}
		
		$returnGraph->getVertex( $oldVertex->getId() )->createEdge( $startVertex );	//Connect last vertex with start vertex
		
		return $returnGraph;
	}
}