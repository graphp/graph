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
		$returnGraph = $this->graph->createGraphCloneEdgeless();				//Copy vertices of original graph
		
		$minimumSpanningTreeAlgorithm = new AlgorithmKruskal($this->graph);		//Create minimum spanning tree
		$minimumSpanningTree = $minimumSpanningTreeAlgorithm->getResultGraph();
		
		$depthFirstSearch = $minimumSpanningTree->getAnyVertex()->searchDepthFirst();	//Depth first search in minmum spanning tree (for the eulerian path)
		
		$startVertex = NULL;
		$oldVertex = NULL;
		
		foreach ($depthFirstSearch as $vertex){									//Connect vertices in order of the depth first search
			
			$vertex = $this->graph->getVertex( $vertex->getId() );					//get vertex from the original graph (not from the depth first search)
																					//i need to clone the edge from the original graph, therefore i need the original edge
			if ($startVertex === NULL){
				$startVertex = $vertex;
			}
			else {
				foreach ($oldVertex->getEdgesTo( $vertex ) as $edge ){				//Get edge to clone //more edges are possible (returns an array)
					$returnGraph->createEdgeClone( $edge );
					break;
				}
			}
			
			$oldVertex = $vertex;
		}

		foreach ($oldVertex->getEdgesTo( $startVertex ) as $edge ){				//Connect last vertex with start vertex //retusn an array
			$returnGraph->createEdgeClone( $edge );
			break;
		}
		
		return $returnGraph;
	}
}