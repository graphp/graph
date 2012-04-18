<?php
class AlgorithmKruskal{
	
	private $graph;
	
	public function __construct(Graph $inputGraph){
 		$this->graph = $inputGraph;
	}

	private $debugMode = false;
	/**
	 *
	 * @return Graph
	 */
	public function getResultGraph(){
		$newGraph = new Graph();
		$colorOfVertices = array();
		$colorCounts = array();
		$colorNext = 0;
		
		//Sortiere Kanten im Graphen
		$sortedEdges = new SplPriorityQueue();
		
		foreach ($this->graph->getEdges() as $edge){							//For all edges
			$sortedEdges->insert($edge, - $edge->getWeight());						//Add edges with negativ Weight because of order in stl
		}
		
		
		//Füge billigste Kanten zu neuen Graphen hinzu und verschmelze teilgragen wenn es nötig ist (keine Kreise)
		//solange ich mehr als einen Graphen habe mit weniger als n-1 kanten (bei n knoten im original)
		foreach ($sortedEdges as $edge){
			//Gucke Kante an:
					
			$vertices = $edge->getVertices();
			$vertexA = $vertices[0];
			$vertexB = $vertices[1];
			
			$aColor = isset($colorOfVertices[$vertexA->getId()]) ? $colorOfVertices[$vertexA->getId()] : NULL;
			$bColor = isset($colorOfVertices[$vertexB->getId()]) ? $colorOfVertices[$vertexB->getId()] : NULL;
			
			//1. weder start noch end gehört zu einem graphen
				//=> neuer Graph mit kanten
			if ( $aColor === NULL && $bColor === NULL ){
				$colorCounts[$colorNext] = 2;
				
				$colorOfVertices[$vertexA->getId()] = $colorNext;
				$colorOfVertices[$vertexB->getId()] = $colorNext;
				
				++$colorNext;
				
				$newVertexA = $newGraph->createVertexFrom($vertexA);
				$newVertexB = $newGraph->createVertexFrom($vertexB);
				$newVertexA->createEdge($newVertexB);
			}
			//4. start xor end gehören zu einem graphen
				//=> erweitere diesesn Graphen
			else if ($aColor === NULL && $bColor !== NULL){						//Only b has color
				
			}
			else if ($aColor !== NULL && $bColor === NULL){						//Only a has color
				
			}
			//3. start und end gehören zu unterschiedlichen graphen
				//=> vereinigung
			else if ($aColor !== $bColor){										//Different color
				
			}
			//2. start und end gehören zum gleichen graphen => zirkel
			//=> nichts machen
		}
		
		
		// Initialize program
// 		$returnGraph =  new Graph();

// 		$edgeQueue = new SplPriorityQueue();
// 		$edgeQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA); // Set extract type to value
// 		// END Initialize program



	}

}
