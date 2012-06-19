<?php

namespace Fhaculty\Graph\Algorithm\MinimumSpanningTree;

use Fhaculty\Graph\Exception\RuntimeException;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\EdgeDirected;
use \SplPriorityQueue;

class Kruskal extends Base{
    
    /**
     * 
     * @var Graph
     */
    private $graph;
    
    public function __construct(Graph $inputGraph){
         $this->graph = $inputGraph;
    }
    
    public function createGraph(){
    	return $this->graph->createGraphCloneEdges($this->getEdges());         //Copy Graph
    }
    
    /**
     *
     * @return array[Edge]
     */
    public function getEdges(){
        //Sortiere Kanten im Graphen
        
        $sortedEdges = new SplPriorityQueue();
        
        foreach ($this->graph->getEdges() as $edge){                            //For all edges
            if(!$edge->isLoop()){                                               // ignore loops (a->a)
                if($edge instanceof EdgeDirected){
                    throw new UnexpectedValueException('Kruskal for directed edges not supported');
                }
                $weight = $edge->getWeight();
                if($weight === NULL){
                    throw new UnexpectedValueException('Kruskal for edges with no weight not supported');
                }
                $sortedEdges->insert($edge, - $weight);                        //Add edges with negativ Weight because of order in stl
            }
        }
        
        if($sortedEdges->isEmpty()){
            throw new RuntimeException('No edges found');
        }
        
        //$sortedEdges = $this->graph->getEdgesOrdered('weight');
        
        $returnEdges = array();
        
        $colorNext = 0;    // next color to assign
        $colorVertices = array(); // array(color1=>array(vid1,vid2,...),color2=>...)
        $colorOfVertices = array(); // array(vid1=>color1,vid2=>color1,...)
        
        //Füge billigste Kanten zu neuen Graphen hinzu und verschmelze teilgragen wenn es nötig ist (keine Kreise)
        //solange ich mehr als einen Graphen habe mit weniger als n-1 kanten (bei n knoten im original)
        foreach ($sortedEdges as $edge){
            //Gucke Kante an:
                    
            $vertices = $edge->getVerticesId();
            
            $aId = $vertices[0];
            $bId = $vertices[1];
            
            $aColor = isset($colorOfVertices[$aId]) ? $colorOfVertices[$aId] : NULL;
            $bColor = isset($colorOfVertices[$bId]) ? $colorOfVertices[$bId] : NULL;
            
            //1. weder start noch end gehört zu einem graphen
                //=> neuer Graph mit kanten
            if ( $aColor === NULL && $bColor === NULL ){
                $colorOfVertices[$aId] = $colorNext;
                $colorOfVertices[$bId] = $colorNext;
                
                $colorVertices[$colorNext] = array($aId,$bId);
                
                ++$colorNext;
                
                $returnEdges []= $edge;                                         // connect both vertices
            }
            //4. start xor end gehören zu einem graphen
                //=> erweitere diesesn Graphen
            else if ($aColor === NULL && $bColor !== NULL){                        //Only b has color
                $colorOfVertices[$aId] = $bColor;                               // paint a in b's color
                $colorVertices[$bColor][]=$aId;
                
                $returnEdges []= $edge;
            }
            else if ($aColor !== NULL && $bColor === NULL){                        //Only a has color
                $colorOfVertices[$bId] = $aColor;                               // paint b in a's color
                $colorVertices[$aColor][]=$bId;
                
                $returnEdges []= $edge;
            }
            //3. start und end gehören zu unterschiedlichen graphen
                //=> vereinigung
            else if ($aColor !== $bColor){                                        //Different color
                $betterColor = $aColor;
                $worseColor  = $bColor;
                
                if(count($colorVertices[$bColor]) > count($colorVertices[$aColor])){ // more vertices with color a => paint all in b in a's color
                    $betterColor = $bColor;
                    $worseColor = $aColor;
                }
                
                foreach($colorVertices[$worseColor] as $vid){                   //search all vertices with color b
                    $colorOfVertices[$vid] = $betterColor;
                    $colorVertices[$betterColor][]=$vid;                        // repaint in a's color
                }
                unset($colorVertices[$worseColor]);                             // delete old color
                
                $returnEdges []= $edge;
            }
            //2. start und end gehören zum gleichen graphen => zirkel
            //=> nichts machen
        }
        
        // definition of spanning tree: number of edges = number of vertices - 1
        // above algorithm does not check isolated edges or may otherwise return multiple connected components => force check
        if (count($returnEdges) !== ( $this->graph->getNumberOfVertices() - 1 ) ){
            throw new UnexpectedValueException('Graph is not connected');
        }
        
        return $returnEdges;
    }
}
