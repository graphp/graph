<?php

class AlgorithmMCFSuccessiveShortestPath extends AlgorithmMCF {

	/**
	 * Calculates the flow for the given Vertex: sum(inflow) - sum(outflow)
	 * 
	 * @param Vertex $vertex where the flow is calculated for
	 * 
	 * @return double flow of Vertex
	 * 
	 * @throws Exception if they are undirected edges
	 */
	private function flow(Vertex $vertex){
		$edges = $vertex->getEdges();
		
		$sumOfFlow = 0;
		
		foreach ($edges as $edge){
			if ( ! ($edge instanceof EdgeDirected)){
				throw new Exception("TODO: undirected edges not suported jet");
			}
			
			if ($edge->isOutgoingEdgeOf($vertex)){
				$sumOfFlow -= $edge->getFlow();
			}
			else{
				$sumOfFlow += $edge->getFlow();
			}
		}
		
		return $sumOfFlow;
	}
	
	/**
	 * @uses AlgorithmMCFSuccessiveShortestPath::flow(Vertex $vertex)
	 * @uses Graph::createGraphClone()
	 * @uses AlgorithmResidualGraph::getResultGraph()
	 * @uses AlgorithmSearchBreadthFirst::getVertices()
	 * @uses AlgorithmSpMooreBellmanFord::getEdgesTo(Vertex $targetVertex)
	 * 
	 * @see AlgorithmMCF::getResultGraph()
	 */
	public function getResultGraph() {
		
		$resultGraph = $this->graph->createGraphClone();
		
		//initial flow of edges
		$edges = $resultGraph->getEdges();
		foreach ($edges as $edge){
			$flow = 0;															//0 if weight of edge is positiv
			
			if ($edge->getWeight() < 0){										//maximal flow if weight of edge is negative
				$flow = $edge->getCapacity();
			}
			
			$edge->setFlow($flow);
		}
		
		while(true)																//return or Exception insite this while
		{
			//create residual graph
			$algRG = new AlgorithmResidualGraph($resultGraph);
			$residualGraph = $algRG->getResultGraph();
			
			//search for a source	
			$vertices = $residualGraph->getVertices();
			
			$sourceVertex = null;
			foreach ($vertices as $vertex){										//forall (just use the first found)
				
				if ($this->flow($vertex) > 0){									//if flow of vertex is positiv => source
					$sourceVertex = $vertex;
					break;
				}
			}
			if ($sourceVertex === null){										//if no source is found the minimum-cost flow is found 
				return $resultGraph;
			}
			
			//search for reachble target from this source
			$algBFS = new AlgorithmSearchBreadthFirst($sourceVertex);			//search for reachable Vertices
			$vertices = $algBFS->getVertices();
			
			$targetVertex = null;
			foreach ($vertices as $vertex){										//forall (just use the first found)
				
				if ($this->flow($vertex) < 0){									//if flow of vertex is negative => target
					$targetVertex = $vertex;
					break;
				}
			}
			if ($targetVertex === null){										//if no target is found the network has not enough capacity
				throw new Exception("The graph has not enough capacity for the minimum-cost flow");
			}
			
			//calculate shortest path between source- and target-vertex
			$algSP = new AlgorithmSpMooreBellmanFord($sourceVertex);
			$edgesOnFlow = $algSP->getEdgesTo($targetVertex);
																				//new flow is the maximal possible flow for this path
			$newflow    = $sourceVertex->getBalance() - $this->flow($sourceVertex);
			$targetFlow = - ($targetVertex->getBalance() - $this->flow($targetVertex));
			
			if ($newflow > $targetFlow){										//minimum of source and target
				$newflow = $targetFlow;
			}
			
			foreach ($edgesOnFlow as $edge){									//minimum of left capacity at path
				$edgeFlow = $edge->getCapacityRemaining();
				
				if ($newflow > $edgeFlow){
					$newflow = $edgeFlow;
				}
			}
			
			//TODO Verändere den Fluss zwischen Quelle und Senke
			
		}
	}
	
}