<?php

class AlgorithmMCFCycleCanceling extends AlgorithmMCF {
	
	public function getResultGraph() {
		
		
		//initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten
		
		
		//while no more paths
		{
		    //1. calculate b-flow
		    //    find sinks and sources	    
			//    create supersource and supersink 
			//    max flow searched
			//2. Create a Residualgraph 
			//    get the residual capacity and the	Residualcosts
			//3. Create a f-augmenting cykel Z in Residualgraph Gf with negativen costs 
			//    if non exists stopp
			//4. Change the b-Flow beside the f-augmenting cykel
			//5. goto step 2
			

		}
		
		return $this->resultGraph;
	}
	

	
}