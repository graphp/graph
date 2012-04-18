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
		
		foreach($this->graph->getVertices() as $vertex){ // copy all vertices to new graph
		    $newGraph->createVertexClone($vertex);
		}
		
		$colorCounts = array();
		$colorOfVertices = array();
		$colorNext = 0;
		
		//Sortiere Kanten im Graphen
		$sortedEdges = new SplPriorityQueue();
		
		foreach ($this->graph->getEdges() as $edge){							//For all edges
		    if($edge instanceof EdgeDirected){
		        throw new Exception('Not supported');
		    }
		    $sortedEdges->insert($edge, - $edge->getWeight());						//Add edges with negativ Weight because of order in stl
		}
		
		
		//Füge billigste Kanten zu neuen Graphen hinzu und verschmelze teilgragen wenn es nötig ist (keine Kreise)
		//solange ich mehr als einen Graphen habe mit weniger als n-1 kanten (bei n knoten im original)
		foreach ($sortedEdges as $edge){
			//Gucke Kante an:
					
			$vertices = $edge->getVertices();
			$vertexA = $vertices[0];
			$vertexB = $vertices[1];
			
			$aId = $vertexA->getId();
			$bId = $vertexB->getId();
			
			$aColor = isset($colorOfVertices[$aId]) ? $colorOfVertices[$aId] : NULL;
			$bColor = isset($colorOfVertices[$bId]) ? $colorOfVertices[$bId] : NULL;
			
			//1. weder start noch end gehört zu einem graphen
				//=> neuer Graph mit kanten
			if ( $aColor === NULL && $bColor === NULL ){
				$colorCounts[$colorNext] = 2;
				
				$colorOfVertices[$aId] = $colorNext;
				$colorOfVertices[$bId] = $colorNext;
				
				++$colorNext;
				
				$newGraph->createEdgeClone($edge);                              // connect both vertices
			}
			//4. start xor end gehören zu einem graphen
				//=> erweitere diesesn Graphen
			else if ($aColor === NULL && $bColor !== NULL){						//Only b has color
				$colorOfVertices[$aId] = $bColor;                               // paint a in b's color
				++$colorCounts[$bColor];
				
				$newGraph->createEdgeClone($edge);
			}
			else if ($aColor !== NULL && $bColor === NULL){						//Only a has color
				$colorOfVertices[$bId] = $aColor;                               // paint b in a's color
				++$colorCounts[$aColor];
				
				$newGraph->createEdgeClone($edge);
			}
			//3. start und end gehören zu unterschiedlichen graphen
				//=> vereinigung
			else if ($aColor !== $bColor){										//Different color
				$betterColor = $aColor;
				$worseColor  = $bColor;
				
				if($colorCounts[$bColor] > $colorCounts[$aColor]){              // more vertices with color a => paint all in b in a's color
				    $betterColor = $bColor;
				    $worseColor = $aColor;
				}
				
			    foreach($colorOfVertices as $vid=>$color){
			        if($color === $worseColor){                                 //search all vertices with color b
			            $colorOfVertices[$vid] = $betterColor;                  // replaint in a's color
			        }
			    }
			    $colorCounts[$betterColor] += $colorCounts[$worseColor];        // update colorcount + old colorcount
			    unset($colorCounts[$worseColor]);                               // delete old color
			    
			    $newGraph->createEdgeClone($edge);
			}
			//2. start und end gehören zum gleichen graphen => zirkel
			//=> nichts machen
		}
		
		if(count($colorCounts) === 1){
		    throw new Exception('Graph is not connected or empty');
		}
		
		return $newGraph;
	}

}
