<?php

class AlgorithmMCFSuccessiveShortestPath extends AlgorithmMCF {
	
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
		
			//Erstelle ResidualGraph
		
			//Suche Quelle
		
			//Suche von Quelle erreichbare Senke
		
			//Berechne Kürzesten Weg zwischen Quelle und Senke
		
			//Verändere den Fluss zwischen Quelle und Senke
			
		//Gebe Graph zurück
	}
	
}