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
	public function getResultGraph(){
		$returnGraph = $this->graph->createGraphCloneEdgeless();
		
		$minimumSpanningTreeAlgorithm = new AlgorithmKruskal($this->graph);
		$minimumSpanningTree = $minimumSpanningTreeAlgorithm->getResultGraph();
		
		$depthFirstSearch = $minimumSpanningTree->getAnyVertex()->searchDepthFirst();
		
		$startVertex = NULL;
		$oldVertex = NULL;
		
		foreach ($depthFirstSearch as $vertex){
			
			$vertex = $this->graph->getVertex( $vertex->getId() );
			
			if ($startVertex === NULL){
				$startVertex = $vertex;
			}
			else {
				foreach ($oldVertex->getEdgesTo( $vertex ) as $edge ){
					$returnGraph->createEdgeClone( $edge );
					break;
				}
//				$returnGraph->getVertex( $oldVertex->getId() )->createEdge( $returnGraph->getVertex( $vertex->getId() ) );
			}
			
			$oldVertex = $vertex;
		}

		foreach ($oldVertex->getEdgesTo( $startVertex ) as $edge ){
			$returnGraph->createEdgeClone( $edge );
			break;
		}		
//		$returnGraph->getVertex( $oldVertex->getId() )->createEdge( $returnGraph->getVertex( $startVertex->getId() ) );	//Connect last vertex with start vertex
		
		return $returnGraph;
	}
}