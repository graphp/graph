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
	 * @param Vertex $destinationVertex the vertex where the flow search ends (destination)
	 */
	public function __construct(Vertex $startVertex, Vertex $destinationVertex){
		if($startVertex === $destinationVertex){
			throw new Exception('Start and destination must not be the same vertex');
		}
		if($startVertex->getGraph() !== $destinationVertex->getGraph()){
			throw new Exception('Start and target vertex have to be in the same graph instance');
		}
		$this->startVertex = $startVertex;
		$this->destinationVertex = $destinationVertex;
		$this->graph = $startVertex->getGraph();

		foreach ($this->graph->getEdges() as $edge){
			$edge->setFlow(0);
		}
	}

	/**
	 * returns max flow graph
	 *
	 * @return Graph
	 */
	public function getResultGraph(){
		$currentGraph = $this->graph->createGraphClone();

		$i = 0;
		do{
			$pathFlow = $this->getGraphShortestPathFlow($currentGraph);         // Get shortest path if NULL-> Done

			if($pathFlow){														// If path exists add the new flow to graph
				$edgeFromFlowPath = Edge::getFirst($pathFlow->getEdges());
				$newFlowValue = $edgeFromFlowPath->getFlow();

				foreach ($pathFlow->getEdges() as $edge){
					$originalEdge = $this->getEdgeSimilarFromGraph($edge, $currentGraph);
					$originalEdge->setFlow($originalEdge->getFlow() + $newFlowValue);
				}

				$residualAlgorithm = new AlgorithmResidualGraph($currentGraph);
				$currentGraph = $residualAlgorithm->getResultGraph();			// Generate new residual graph and repeat
			}
				
				
				
		} while($pathFlow);

		measure(function() use (&$currentGraph){
			return $currentGraph;
		},'FINALResidualgraph');
		
		// Generate the full flow graph from the final residual graph (handled internal: with the initialGraph)
		return $this->getFlowGraphFromResidualGraph($currentGraph);
	}

	/**
	 * returns max flow value
	 *
	 * @return double
	 */
	public function getMaxFlowValue(){
		$resultGraph = $this->getResultGraph();

		$start = $resultGraph->getVertex($this->startVertex->getId());
		$maxFlow = 0;
		foreach ($start->getOutgoingEdges() as $edge){
			$maxFlow = $maxFlow + $edge->getWeight();
		}
		return $maxFlow;
	}

	private function getFlowGraphFromResidualGraph($residualGraph){

		//process the original graph and create a new graph that contains the flow
		$resultGraph = $this->graph->createGraphCloneEdgeless();

		$originalGraphEdgesArray = $this->graph->getEdges();
		foreach ($originalGraphEdgesArray as $edge){
			// For every edge in the residual graph,
			// that has an inversed edge in the original graph:
			// Insert the inversed residual edge into the new graph

			// Inverse the edge
			$residualEdge = $this->getEdgeSimilarFromGraph($edge,$residualGraph,true);

			// Add inversed edge to return graph
			$newFlowEdge = $resultGraph->createEdgeClone($edge);

			// Set flow of the edge
			if($residualEdge != NULL){
				$newFlowEdge->setFlow($residualEdge->getCapacity());
			}
			else{
				$newFlowEdge->setWeight(0);
			}
		}
		return $resultGraph;
	}

	/**
	 * get the shortest path flow (by count of edges)
	 *
	 * @param Graph $currentGraph
	 * @return Graph if path exists OR NULL
	 */
	private function getGraphShortestPathFlow($currentGraph)
	{

		$startVertex = $currentGraph->getVertex($this->startVertex->getId());
		$destinationVertex = $currentGraph->getVertex($this->destinationVertex->getId());

		// 1. Search _shortest_ (number of hops and cheapest) path from s -> t
		$breadthSearchAlg = new AlgorithmSearchBreadthFirst($startVertex);
		$path = $breadthSearchAlg->getGraphPathTo($destinationVertex);

		if($path === NULL){
			//no path found return null
			return NULL;
		}

		// 2. get max flow from path
		$bottleNeckEdge = Edge::getFirst($path->getEdges(),Edge::ORDER_CAPACITY);
		$maxFlowValue = $bottleNeckEdge->getCapacity();

		if($maxFlowValue == 0){
			//echo "stop flow value is 0\n";
			return null;
		}
			
		// 3. create graph with shortest path and max flow as edge values
		foreach($path->getEdges() as $edge){
			$edge->setFlow($maxFlowValue);
		}
			
		return $path;
	}


	
	/**
	 * Extracts a (optional: inversed) edge from the given graph
	*
	* @param Graph $edge
	* @param Graph $newGraph
	* @param Boolean $inverse
	* @return Graph
	*/
	private function getEdgeSimilarFromGraph($edge,$newGraph,$inverse=false){
		// Extract endpoints from edge
		$originalStartVertexArray = $edge->getStartVertices();
		$originalStartVertex = array_shift($originalStartVertexArray);

		$originalTargetVertexArray = $edge->getTargetVertices();
		$originalTargetVertex = array_shift($originalTargetVertexArray);

		// swap them if inverse wanted
		if($inverse){
			$temp = $originalStartVertex;
			$originalStartVertex = $originalTargetVertex;
			$originalTargetVertex = $temp;
		}

		// Get original vertices from resultgraph
		$residualGraphEdgeStartVertex = $newGraph->getVertex($originalStartVertex->getId());
		$residualGraphEdgeTargetVertex = $newGraph->getVertex($originalTargetVertex->getId());

		// Now get the edge
		$residualEdgeArray = $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);

		// Check for parallel edges
		$countOfFoundEdges = count($residualEdgeArray);
		if($countOfFoundEdges === 0){											// If no edge found
			return NULL;
		} else if($countOfFoundEdges !== 1){
			throw new Exception('More than one cloned edge? Parallel edges (multigraph) not supported');
		}

		return $residualEdgeArray[0];
	}
}
