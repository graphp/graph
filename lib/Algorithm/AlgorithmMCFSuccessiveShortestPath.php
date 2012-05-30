<?php

class AlgorithmMCFSuccessiveShortestPath extends AlgorithmMCF {
	
	public function getEdges() {
		
		//Check if balance is ok
		$vertices = $this->graph->getVertices();
		$balance = 0;
		foreach ($vertices as $vertex) {										//Sum for all vertices of value
			$balance += $vertex->getValue();
		}
		if ($balance !== 0) {													//If the sum is 0 => same "in-flow" as "out-flow"
			throw new Exception("The graph is not balanced");
		}
		
		//initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten
		
		
		
			//Erstelle ResidualGraph
		
			//Suche Quelle
		
			//Suche von Quelle erreichbare Senke
		
			//Berechne Kürzesten Weg zwischen Quelle und Senke
		
			//Verändere den Fluss zwischen Quelle und Senke
			
		//Gebe Graph zurück
	}
	
}