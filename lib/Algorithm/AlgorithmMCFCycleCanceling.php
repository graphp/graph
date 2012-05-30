<?php

class AlgorithmMCFCycleCanceling extends AlgorithmMCF {
	
	public function getResultGraph() {
		
		$resultGraph = $this->graph->createGraphClone();
		//initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten
		
		$edges = $resultGraph->getEdges();										//initial flow of edges
		foreach ($edges as $edge){
			$flow = 0;															//0 if weight of edge is positiv
			
			if ($edge->getWeight() < 0){										//maximal flow if weight of edge is negative
				$flow = $edge->getCapacity();
			}
			
			$edge->setFlow($flow);
		}
		
		//while no more paths
		{
			//1. calculate b-flow
			//    add supersource and supersink 
			//    max flow searched
			//2. Create a Residualgraph 
			//    get the residual capacity and the	Residualcosts
			//3. Create a f-augmenting cykel Z in Residualgraph Gf with negativen costs 
			//    if non exists stopp
			//4. Change the b-Flow beside the f-augmenting cykel
			//5. goto step 2
			

		}
		
		return $resultGraph;
	}
	
}