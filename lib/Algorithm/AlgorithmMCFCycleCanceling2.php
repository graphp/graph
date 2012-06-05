<?php

class AlgorithmMCFCycleCanceling2 extends AlgorithmMCF {
   
    public function createGraph() {
    	$this->checkBalance();

        // create resulting graph with supersource and supersink
        $resultGraph = $this->graph->createGraphClone();

        $superSource = $resultGraph->createVertex()->setLayout('label','s*');
        $superSink   = $resultGraph->createVertex()->setLayout('label','t*');

        $sumBalance = 0;

        // connect supersource s* and supersink* with all "normal" sources and sinks
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
        $algMaxFlow = new AlgorithmMaxFlowEdmondsKarp($superSource,$superSink);
        $flow = $algMaxFlow->getMaxFlowValue();

        if($flow !== $sumBalance){
            throw new Exception('(s*,t*)-flow of '.$flow.' has to equal sumBalance '.$sumBalance);
        }

        
        $resultGraph = $algMaxFlow->createGraph();
        
        while(true){
            //create residual graph
            $algRG = new AlgorithmResidualGraph($resultGraph);
            $residualGraph = $algRG->createGraph();
            $cloneSink = $residualGraph->getVertex($superSink->getId());
            
            //get negative cycle
            $alg = new AlgorithmSpMooreBellmanFord($cloneSink);
            try {
                $clonedEdges = $alg->getCycleNegative()->getEdges();
            }
            catch (Exception $ignore) {
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
        
        $resultGraph->getVertex($superSink->getId())->destroy();
        $resultGraph->getVertex($superSource->getId())->destroy();
        
        return $resultGraph;
    }
}