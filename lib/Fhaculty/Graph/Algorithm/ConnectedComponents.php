<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchBreadthFirst;

use Fhaculty\Graph\Exception\InvalidArgumentException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class ConnectedComponents extends Base{
    
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
    
    /**
     * create subgraph with all vertices connected to given vertex (i.e. the connected component of ths given vertex)
     * 
     * @param Vertex $vertex
     * @return Graph
     * @throws InvalidArgumentException if given vertex is not from same graph
     * @uses AlgorithmSearchBreadthFirst::getVerticesIds()
     * @uses Graph::createGraphCloneVertices()
     */
    public function createGraphComponentVertex(Vertex $vertex){
        if($vertex->getGraph() !== $this->graph){
            throw new InvalidArgumentException('This graph does not contain the given vertex');
        }
        return $this->graph->createGraphCloneVertices($this->createSearch($vertex)->getVertices());
    }
    
    private function createSearch(Vertex $vertex){
        $alg = new SearchBreadthFirst($vertex);
        return $alg->setDirection(SearchBreadthFirst::DIRECTION_BOTH); // follow into both directions (loosely connected)
    }
    
    /**
     * check whether this graph consists of only a single component
     * 
     * this is faster than calling getNumberOfComponents(), as it only has to
     * count all vertices in one component to see if the graph consists of only
     * a single component
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
    
    /**
     * separate input graph into separate independant and unconnected graphs
     * 
     * @return Graph[]
     * @uses Graph::getVertices()
     * @uses AlgorithmSearchBreadthFirst::getVertices()
     */
    public function createGraphsComponents(){
    	$visitedVertices = array();
    	$graphs = array();
    
    	foreach ($this->graph->getVertices() as $vid=>$vertex){               //for each vertices
    		if ( ! isset( $visitedVertices[$vid] ) ){                          //did I visit this vertex before?
    
    			$alg = $this->createSearch($vertex);
    			$newVertices = $alg->getVertices();                          //get all vertices of this component
    
    			foreach ($newVertices as $vid=>$unusedVertex){                //mark the vertices of this component as visited
    				$visitedVertices[$vid] = true;
    			}
    
    			$graphs []= $this->graph->createGraphCloneVertices($newVertices);
    		}
    	}
    
    	return $graphs;
    }
}
