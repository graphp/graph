<?php
class AlgorithmSpDijkstra{
	public function __construct(Graph $inputGraph, Vertex $startVertex){
		$this->startGraph = $inputGraph;
		$this->startVertex = $startVertex;
	}

	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){

		// Initialise programm
		$distanceFromStartTo  = Array();
		$distanceFromStartTo[$this->startVertex] = 0;

		$predecessorTo  = Array();
		$marked  = Array();

		$edgePriorityQueue = new SplPriorityQueue();
		
		foreach ($this->startVertex->getEdges() as $edge){
			// Füge nur Kanten hinzu die von $this->startVertex abgehen
		}
		
		
		// Wiederhole bis alle vertices hinzugefügt
		$countOfVertices = 0;
		$totalCountOfVertices = $this->startGraph->count();
		while ($countOfVertices< $totalCountOfVertices){

			// Hole aktuell am günstigsten erreicharen Knoten mittels edgePrioList
				
			// markiere diesen Knoten (der kürzeste weg ist bestimmt) & $countOfVertices++
				
			// Füge die Nachbarn von diesem Knoten hinzu -> set($distanceFromStartTo & $predecessorTo)
			//		falls Nachbar bereit gesetzt war und neue Verbindung günstiger -> Update($distanceFromStartTo & $predecessorTo)
		}
	}
}