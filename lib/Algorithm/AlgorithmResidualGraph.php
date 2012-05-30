<?php

class AlgorithmResidualGraph extends Algorithm{
	private $graph;
	private $keepNullCapacity = false;

	public function __construct(Graph $graph){
		$this->graph = $graph;
	}

	public function setKeepNullCapacity($toggle){
		$this->keepNullCapacity = !!$toggle;
		return $this;
	}

	/**
	 * create residual graph
	 *
	 * @throws Exception if input graph has undirected edges or flow/capacity is not set
	 * @return Graph
	 * @uses Graph::createGraphCloneEdgeless()
	 * @uses Graph::createEdgeClone()
	 * @uses Graph::createEdgeCloneInverted()
	 */
	public function getResultGraph(){
		 
		$newgraph = $this->graph->createGraphCloneEdgeless();

		foreach($this->graph->getEdges() as $edge){
			if(!($edge instanceof EdgeDirected)){
				throw new Exception('Edge is undirected');
			}

			$flow = $edge->getFlow();
			if($flow === NULL){
				throw new Exception('Flow not set');
			}

			$capacity = $edge->getCapacity();
			if($capacity === NULL){
				throw new Exception('Capacity not set');
			}

			// capacity is still available, clone remaining capacity into new edge
			if($this->keepNullCapacity || $flow < $capacity){
				$newEdge = $newgraph->createEdgeClone($edge)->setFlow(0)->setCapacity($capacity - $flow);
				$this->mergeParallelEdges($newEdge);
			}

			// flow is set, clone current flow as capacity for back-flow into new inverted edge (opposite direction)
			if($this->keepNullCapacity || $flow > 0){
				$newEdge = $newgraph->createEdgeCloneInverted($edge)->setFlow(0)->setCapacity($flow);
				$this->mergeParallelEdges($newEdge);
				// if weight is set, use negative weight for back-edges
				if($newEdge->getWeight() !== NULL){
					$newEdge->setWeight(-$newEdge->getWeight());
				}
			}
		}
		return $newgraph;
	}

	private function mergeParallelEdges(Edge $newEdge){
		$parallelEdges = $newEdge->getEdgesParallel();
		$countParallelEdges = count($parallelEdges);
		if($countParallelEdges > 0){
			 
			$mergedCapacity = 0;
			 
			foreach ($parallelEdges as $parallelEdge){
				$mergedCapacity += $parallelEdge->getCapacity();
			}
			 
			$newEdge->setCapacity($newEdge->getCapacity() + $mergedCapacity);
			 
			foreach ($parallelEdges as $parallelEdge){
				$parallelEdge->destroy();
			}
		}
	}
}
