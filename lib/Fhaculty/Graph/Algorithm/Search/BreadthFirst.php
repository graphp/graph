<?php

namespace Fhaculty\Graph\Algorithm\Search;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;

class BreadthFirst extends Base{
    /**
     *
     * @return array[Vertex]
     */
    public function getVertices(){
        $queue = array($this->startVertex);
        $mark = array($this->startVertex->getId() => true);                            //to not add vertices twice in array visited
        $visited = array();                                                        //visited vertices
        
        do{
            $t = array_shift($queue);                                                // get first from queue
            $visited[$t->getId()]= $t;                                                //save as visited

            $vertices = $this->getVerticesAdjacent($t);                                //get next vertices
            foreach($vertices as $id=>$vertex){
                if(!isset($mark[$id])){                                                    //if not "toughed" before
                    $queue[] = $vertex;                                                        //add to queue
                    $mark[$id] = true;                                                        //and mark
                }
            }

        }while($queue);                                                            //untill queue is empty

        return $visited;
    }

    /**
     * Get a path from algorithms $startVertex to given $destination vertex
     *
     * @param Vertex $destinationVertex
     * @return Graph with lowest vertex count between start and destination OR NUll if no path exists
     */
    public function getGraphPathTo(Vertex $destinationVertex){
        $queue = array($this->startVertex);                                         //Start vertex
        $mark = array($this->startVertex->getId() => true);                            //to not add vertices twice in array visited
         
        $deepSearchThree = $this->startVertex->getGraph()->createGraphCloneEdgeless();   //create copy of graph without edges
        $visited = array();                                                        //visited vertices

        $pathToDestinationVertexFound = false;

        do{
            $t = array_shift($queue);                                            //get first from queue
            $visited[$t->getId()]= $t;                                            //save as visited

            $vertices = $this->getVerticesAdjacent($t);                                //get next vertices
            foreach($vertices as $id=>$vertex){
                if(!isset($mark[$id])){                                            //if not "toughed" before
                    $queue[] = $vertex;                                            //add to queue
                    $mark[$id] = true;                                            //and mark
                     
                    $oldEdgeArray=$t->getEdgesTo($vertex);
                    $oldEdge=array_shift($oldEdgeArray);

                    $newEdge=$deepSearchThree->createEdgeClone($oldEdge);

                }
            }

            if(isset($mark[$destinationVertex->getId()])){                               //stop if destination node is reached
                $queue=null;
                $pathToDestinationVertexFound = true;
            }

        } while($queue);                                                            //untill queue is empty
         
        if(!$pathToDestinationVertexFound){
            return NULL;
        }

        //remove not used edges
        $destinationVertexInNewGraph = $deepSearchThree->getVertex($destinationVertex->getId());
         
         
        // create path
        $path = new Graph();
        $path->createVertexClone($destinationVertex);                 //add clone of destination vertex to path
        $currentVertex=$destinationVertexInNewGraph;

        //run over the tree
        do{                                                                     // run from the buttom to top to the tree and add the predecessor until the startvertex is reached
            $edge=$currentVertex->getEdgesIn();                            // can only one because of the deepsearch tree

            $upperVertex=$edge[0]->getVertexFromTo($currentVertex);
            $path->createVertexClone($upperVertex);                             // clone upper vertex to graph
            $path->createEdgeClone($edge[0]);                                   // clone connecting edge
             
            $currentVertex=$upperVertex;

        } while($currentVertex->getId()!==$this->startVertex->getId());
        
        return $path;
    }
}
