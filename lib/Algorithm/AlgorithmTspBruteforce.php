<?php

class AlgorithmTspBruteforce{
    /**
     * 
     * @var Graph
     */
    private $graph;
    
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
        
        $this->startVertex = $this->graph->getVertex(0);
        
        $result = $this->step($this->startVertex,
                              0,
                              array($this->startVertex => true),
                              array()
                  );
        
        if($result === NULL){
            throw new Exception();
        }
        
        $graph = $this->graph->createGraphCloneEdgeless();
        foreach($edges as $edge){
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
        if($target === $this->startVertex && count($visitedEdges) === $this->numEdges){ // kreis geschlossen am Ende
            return array($totalWeight,$visitedEdges);
        }
        
        if(isset($visitedVertices[$target->getId()])){ // weiter verzweigen in alle vertices
            return NULL;
        }
        $visitedVertices[$vertex->getId()] = true;
        
        $bestResult = NULL;
        
        foreach($vertex->getEdges() as $edge){
            $target = $edge->getVertexToFrom($vertex);
            
            $weight = $edge->getWeight();
            if($weight === NULL){
                throw new Exception('Unweighted edges not supported');
            }
            
            // TODO: branch and bound if $totalWeight+$weight > $bestWeight
            
            $result = $this->step($target,
                                  $totalWeight + $weight,
                                  $visitedVertices + array($target->getId() => true),
                                  array_merge($visitedEdges,array($edge))
                      );
            
            if($result !== NULL){ // new result found
                if($bestResult === NULL || $result[0] < $bestResult[0]){ // either first result or best result
                    $bestResult = $result;
                }
            }
        }
        
        return $bestResult;
    }
}