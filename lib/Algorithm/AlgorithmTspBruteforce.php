<?php

class AlgorithmTspBruteforce{
    /**
     * 
     * @var Graph
     */
    private $graph;
    
    /**
     * best weight so for (used for branch-and-bound)
     * 
     * @var number|NULL
     */
    private $bestWeight;
    
    /**
     * reference to start vertex
     * 
     * @var Vertex
     */
    private $startVertex;
    
    /**
     * total number of edges needed
     * 
     * @var int
     */
    private $numEdges;
    
    /**
     * 
     * @param unknown_type $graph
     */
    public function __construct($graph){
        $this->graph = $graph;
    }
    
    public function getResultGraph(){
        $this->numEdges = $this->graph->getNumberOfVertices();
        if($this->numEdges < 3){
            throw new Exception('Needs at least 3 vertices');
        }
        
        // numEdges 3-12 should work
        
        $this->bestWeight = NULL;
        $this->startVertex = $this->graph->getVertex(0);
        
        $result = $this->step($this->startVertex,
                              0,
                              array(),
                              array()
                  );
        
        if($result === NULL){
            throw new Exception('No resulting solution for TSP found');
        }
        
        $graph = $this->graph->createGraphCloneEdgeless();
        foreach($result as $edge){
            $graph->createEdgeClone($edge);
        }
        return $graph;
    }
    
    /**
     * 
     * @param Vertex                $vertex      current point-of-view
     * @param number                $totalWeight total weight (so far)
     * @param array[mixed=>boolean] $visitedVertices
     * @param array[Edge]           $visitedEdges
     * @return array[Edge]
     */
    private function step($vertex,$totalWeight,$visitedVertices,$visitedEdges){
        if($this->bestWeight !== NULL && $totalWeight >= $this->bestWeight){ // stop recursion if best result is exceeded (branch and bound)
            return NULL;
        }
        if($vertex === $this->startVertex && count($visitedEdges) === $this->numEdges){ // kreis geschlossen am Ende
            return $visitedEdges;
        }
        
        if(isset($visitedVertices[$vertex->getId()])){                          // only visit each vertex once
            return NULL;
        }
        $visitedVertices[$vertex->getId()] = true;
        
        $bestResult = NULL;
        
        foreach($vertex->getEdges() as $edge){                                  // weiter verzweigen in alle vertices
            $target = $edge->getVertexToFrom($vertex);
            
            $weight = $edge->getWeight();
            if($weight === NULL){
                throw new Exception('Unweighted edges not supported');
            }
            
            $result = $this->step($target,
                                  $totalWeight + $weight,
                                  $visitedVertices,
                                  array_merge($visitedEdges,array($edge))
                      );
            
            if($result !== NULL){ // new result found
                $bestResult = $result;
            }
        }
        
        return $bestResult;
    }
}