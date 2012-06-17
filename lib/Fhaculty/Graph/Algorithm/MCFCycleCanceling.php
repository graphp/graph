<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Edge;
use Fhaculty\Graph\Algorithm\MaxFlow\EdmondsKarp as MaxFlowEdmondsKarp;
use \Exception;

class MCFCycleCanceling extends MCF {

    public function createGraph() {
    	$this->checkBalance();

        // create resulting graph with supersource and supersink
        $resultGraph = $this->graph->createGraphClone();

        $superSource = $resultGraph->createVertex()->setLayout('label','s*');
        $superSink   = $resultGraph->createVertex()->setLayout('label','t*');

        $sumBalance = 0;

        // connect supersource s* and supersink t* with all "normal" sources and sinks
        foreach($resultGraph->getVertices() as $vertex){
            $flow = $vertex->getBalance(); //$vertex->getFlow();
            $b = abs($vertex->getBalance());
            if($flow > 0){ // source
                $superSource->createEdgeTo($vertex)->setCapacity($b);

                $sumBalance += $flow;
            }else if($flow < 0){ // sink
                $vertex->createEdgeTo($superSink)->setCapacity($b);
            }
        }

        // calculate (s*,t*)-flow
        $algMaxFlow = new MaxFlowEdmondsKarp($superSource,$superSink);
        $flow = $algMaxFlow->getFlowMax();

        if($flow !== $sumBalance){
            throw new Exception('(s*,t*)-flow of '.$flow.' has to equal sumBalance '.$sumBalance);
        }


        $resultGraph = $algMaxFlow->createGraph();

        while(true){
            //create residual graph
            $algRG = new ResidualGraph($resultGraph);
            $residualGraph = $algRG->createGraph();

            //get negative cycle
            $alg = new DetectNegativeCycle($residualGraph);
            try {
                $clonedEdges = $alg->getCycleNegative()->getEdges();
            }
            catch (Exception $ignore) {                                        // no negative cycle found => end algorithm
                break;
            }

            //calculate maximal possible flow = minimum capacity remaining for all edges
            $newFlow = Edge::getFirst($clonedEdges,Edge::ORDER_CAPACITY_REMAINING)->getCapacityRemaining();

            //set flow on original graph
            foreach ($clonedEdges as $clonedEdge) {
                try {
            	    $edge = $resultGraph->getEdgeClone($clonedEdge);            //get edge from clone
            	    $edge->addFlow( $newFlow );                                 //add flow
                } catch(Exception $ignor) {                                     //if the edge doesn't exists use the residual edge
                    $edge = $resultGraph->getEdgeClone($clonedEdge, true);
                    $edge->addFlow( - $newFlow);                                //remove flow
                }
            }
        }
        
        // destroy temporary supersource and supersink again
        $resultGraph->getVertex($superSink->getId())->destroy();
        $resultGraph->getVertex($superSource->getId())->destroy();

        return $resultGraph;
    }
}
