<?php

class AlgorithmBipartit extends Algorithm{
    /**
     * input graph to operate on
     * 
     * @var Graph
     */
    private $graph;
    
    public function __construct(Graph $graph){
        $this->graph = $graph;
    }
    
    /**
     * check whether this graph is bipartit
     * 
     * @return boolean
     * @uses AlgorithmBipartit::getColors()
     */
    public function isBipartit(){
        try{
            $this->getColors();
            return true;
        }
        catch(Exception $ignore){ }
        return false;
    }
    
    /**
     * get map of vertex ID to vertex color
     * 
     * @return array[int]
     * @throws Exception if graph is not bipartit
     * @uses AlgorithmBipartit::checkVertex() for every vertex not already colored
     */
    public function getColors(){
        $colors = array();
        
        foreach($this->graph->getVertices() as $vid=>$vertex){
        	if(!isset($colors[$vid])){
        		$colors[$vid] = 0;
    			$this->checkVertex($vertex,0, $colors);
        	}
        }
        return $colors;
    }
    
    /**
     * get groups of vertices per color
     * 
     * @return array[array[Vertex]]
     */
    public function getColorVertices(){
        $colors = $this->getColors();
        $ret = array(0=>array(),1=>array());
        
        foreach($this->graph->getVertices() as $vid=>$vertex){
            $ret[$color[$vid]][$vid] = $vertex;
        }
        return $ret;
    }
    
    /**
     * create new graph with valid groups set according to bipartition colors
     * 
     * @return Graph
     * @throws Exception if graph is not bipartit
     * @uses AlgorithmBipartit::getColors()
     * @uses Graph::createGraphClone()
     * @uses Vertex::setGroup()
     */
    public function createGraphGroups(){
        $colors = $this->getColors();
        
        $graph = $this->graph->createGraphClone();
        foreach($graph->getVertices() as $vid=>$vertex){
            $vertex->setGroup($colors[$vid]);
        }
        
        return $graph;
    }
    
    private function checkVertex(Vertex $vertex,$color,&$colors){
        $nextColor = 1-$color;
        foreach($vertex->getVerticesEdge() as $vid=>$nextVertex){
            if(!isset($colors[$vid])){
                $colors[$vid] = $nextColor;
                $this->checkVertex($nextVertex,$nextColor, $colors);
            }else if($colors[$vid] !== $nextColor){
                throw new Exception('Graph is not bipartit');
            }
        }
    }
}