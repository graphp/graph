<?php

class AlgorithmConnectedComponents extends Algorithm{
    
    /**
     * 
     * @var Graph
     */
    private $graph;
    
    /**
     * 
     * @param Graph $graph
     */
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }
    
    private function createSearch(Vertex $vertex){
        $alg = new AlgorithmSearchBreadthFirst($vertex);
        return $alg->setDirection(AlgorithmSearch::DIRECTION_BOTH); // follow into both directions (loosely connected)
    }
    
    /**
     * check whether this graph consists of only a single component
     * 
     * could be improved by not checking for actual number of components but stopping when there's more than one
     * 
     * @return boolean
     * @uses AlgorithmSearchBreadthFirst::getNumberOfVertices()
     */
    public function isSingle(){
        $alg = $this->createSearch($this->graph->getVertexFirst());
        return ($this->graph->getNumberOfVertices() === $alg->getNumberOfVertices());
    }
    
    /**
     * @return int number of components
     * @uses Graph::getVertices()
     * @uses AlgorithmSearchBreadthFirst::getVerticesIds()
     */
    public function getNumberOfComponents(){
        $visitedVertices = array();
        $components = 0;
        
        foreach ($this->graph->getVertices() as $vid=>$vertex){               //for each vertices
            if ( ! isset( $visitedVertices[$vid] ) ){                          //did I visit this vertex before?
                
                $newVertices = $this->createSearch($vertex)->getVerticesIds();  //get all vertices of this component
                
                ++$components;
                
                foreach ($newVertices as $vid){                               //mark the vertices of this component as visited
                    $visitedVertices[$vid] = true;
                }
            }
        }
        
        return $components;                                                    //return number of components
    }
}
