<?php

namespace Fhaculty\Graph\Algorithm\MaximumMatching;

use Fhaculty\Graph\Algorithm\MaxFlow\EdmondsKarp as MaxFlowEdmondsKarp;
use Fhaculty\Graph\Algorithm\Groups;
use Fhaculty\Graph\Exception;

class Flow extends Base {

    public function createGraph() {
//         if($this->graph->isDirected()){
//             throw new Exception('Input graph must not be directed');
//         }
        
        $alg = new Groups($this->graph);
        if(!$alg->isBipartit()){
            throw new Exception\RuntimeException('Input graph does not have bipartit groups assigned to each vertex. Consider Using "AlgorithmBipartit::createGraph()" first');
        }
        
        // create resulting graph with supersource and supersink
        $resultGraph = $this->graph->createGraphClone();
         
        $maxMatchingValue = $resultGraph->getNumberOfEdges();

        $vertices = $resultGraph->getVertices(); // get all vertices
        // above $vertices does NOT contain supersource and supersink, because
        // we want to skip over them as they do not have a partition assigned
        
        $superSource = $resultGraph->createVertex()->setLayout('label','s*');
        $superSink   = $resultGraph->createVertex()->setLayout('label','t*');
        
        $groups = $alg->getGroups();
        $groupA = $groups[0];
        $groupB = $groups[1];
        
        // connect supersource s* to set A and supersink t* to set B
        foreach($vertices as $vertex){
            $group = $vertex->getGroup();
            
            if($group === $groupA){ // source
                $superSource->createEdgeTo($vertex)->setCapacity($maxMatchingValue);
            } else if($group === $groupB){ // sink
                $vertex->createEdgeTo($superSink)->setCapacity($maxMatchingValue);
            } else {
                throw new Exception\RuntimeException('Unknown set: ' + $belongingSet);
            }
        }

        // All capacities to 1 (according to algorithm)
        foreach ($resultGraph->getEdges() as $edge){
            $edge->setCapacity(1)->setFlow(0);
        }
        
//         visualize($resultGraph);

        // calculate (s*,t*)-flow
        $algMaxFlow = new MaxFlowEdmondsKarp($superSource,$superSink);
        $resultGraph = $algMaxFlow->createGraph();

        // destroy temporary supersource and supersink again
        $resultGraph->getVertex($superSink->getId())->destroy();
        $resultGraph->getVertex($superSource->getId())->destroy();
        
        // Remove non matchings
        foreach($resultGraph->getEdges() as $edge){
        	if($edge->getFlow() == 0) {
        		$edge->destroy();
        	} else {
        	    $edgeOriginal = $this->graph->getEdgeClone($edge);
        	    $edge->setCapacity($edgeOriginal->getCapacity());
        	    $edge->setFlow($edgeOriginal->getFlow());
        	}
        }
        
        return $resultGraph;
    }
}
