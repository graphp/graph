<?php

namespace Fhaculty\Graph\Algorithm\MaximumMatching;

use Fhaculty\Graph\Exception\LogicException;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Algorithm\MaxFlow\EdmondsKarp as MaxFlowEdmondsKarp;
use Fhaculty\Graph\Algorithm\Groups;
use Fhaculty\Graph\Exception;

class Flow extends Base {

    public function getEdges() {
//         if($this->graph->isDirected()){
//             throw new UnexpectedValueException('Input graph contains directed edges');
//         }
        
        $alg = new Groups($this->graph);
        if(!$alg->isBipartit()){
            throw new UnexpectedValueException('Input graph does not have bipartit groups assigned to each vertex. Consider Using "AlgorithmBipartit::createGraph()" first');
        }
        
        // create temporary flow graph with supersource and supersink
        $graphFlow = $this->graph->createGraphCloneEdgeless();

        $vertices = $graphFlow->getVertices(); // get all vertices
        // above $vertices does NOT contain supersource and supersink, because
        // we want to skip over them as they do not have a partition assigned
        
        $superSource = $graphFlow->createVertex()->setLayout('label','s*');
        $superSink   = $graphFlow->createVertex()->setLayout('label','t*');
        
        $groups = $alg->getGroups();
        $groupA = $groups[0];
        $groupB = $groups[1];
        
        // connect supersource s* to set A and supersink t* to set B
        foreach($vertices as $vertex){
            $group = $vertex->getGroup();
            
            if($group === $groupA){ // source
                $superSource->createEdgeTo($vertex)->setCapacity(1)->setFlow(0);
                
                // temporarily create edges from A->B for flow graph
                $originalVertex = $this->graph->getVertex($vertex->getId());
                foreach($originalVertex->getVerticesEdgeTo() as $vertexTarget){
                    $vertex->createEdgeTo($graphFlow->getVertex($vertexTarget->getId()))->setCapacity(1)->setFlow(0);
                }
            } else if($group === $groupB){ // sink
                $vertex->createEdgeTo($superSink)->setCapacity(1)->setFlow(0);
            } else {
                throw new LogicException('Should not happen. Unknown set: ' + $belongingSet);
            }
        }
        
//         visualize($resultGraph);

        // calculate (s*,t*)-flow
        $algMaxFlow = new MaxFlowEdmondsKarp($superSource,$superSink);
        $resultGraph = $algMaxFlow->createGraph();

        // destroy temporary supersource and supersink again
        $resultGraph->getVertex($superSink->getId())->destroy();
        $resultGraph->getVertex($superSource->getId())->destroy();
        
        $returnEdges = array();
        foreach($resultGraph->getEdges() as $edge){
            if($edge->getFlow() > 0){ // only keep matched edges
                $originalEdge = $this->graph->getEdgeClone($edge);
                $returnEdges []= $originalEdge;
            }
        }
        return $returnEdges;
    }
}
