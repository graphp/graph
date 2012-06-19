<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Exception\RuntimeException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class Bipartit extends Base{
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
        catch(RuntimeException $ignore){ }
        return false;
    }
    
    /**
     * checks whether the input graph's vertex groups are a valid bipartition
     * 
     * @return boolean
     * @uses AlgorithmGroups::isBipartit()
     */
    public function isBipartitGroups(){
        $alg = new Groups($this->graph);
        return $alg->isBipartit();
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
            $ret[$colors[$vid]][$vid] = $vertex;
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
                throw new UnexpectedValueException('Graph is not bipartit');
            }
        }
    }
}