<?php

class AlgorithmDetectNegativeCycle extends Algorithm{

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
     * check if the input graph has any negative cycles
     * 
     * @return boolean
     * @uses AlgorithmDetectNegativeCycle::getCycleNegative()
     */
    public function hasCycleNegative(){
        try{
            $this->getCycleNegative();
            return true;                                                      // cycle was found => okay
        }
        catch(Exception $ignore){}                                             // no cycle found
        return false;
    }
    
    /**
     * Searches all vertices for the first negative cycle
     *
     * @return NegativeCycleException
     * @throws Exception if there isn't a negative cycle
     * @uses AlgorithmSpMooreBellmanFord::getCycleNegative()
     */
    public function getCycleNegative(){
        foreach($this->graph->getVertices() as $vertex){                      // check for all vertices
            $alg = new AlgorithmSpMooreBellmanFord($vertex);                   // start MBF algorithm on current vertex
            try{
                return $alg->getCycleNegative();                               // try to get negative cycle for current vertex
            }
            catch(Exception $ignore){ }                                        // no cycle found, check next vertex...
        }                                                                       // no more vertices to check => abort
        throw new Exception("No negative cycle found");
    }
    
    /**
     * create new graph clone with only vertices and edges in negative cycle
     * 
     * @return Graph
     * @throws Exception if there's no negative cycle
     * @uses AlgorithmDetectNegativeCycle::getCycleNegative()
     * @uses NegativeCycleException::createGraph()
     */
    public function createGraph(){
        return $this->getCycleNegative()->createGraph();
    }
}
