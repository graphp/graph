<?php

class AlgorithmMCFCycleCanceling extends AlgorithmMCF {
    
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
        $alg = new AlgorithmMaxFlowEdmondsKarp($superSource,$superSink);
        $flow = $alg->getMaxFlowValue();
        
        visualize($resultGraph);
        visualize($alg->createGraph());
        
        if($flow !== $sumBalance){
            throw new Exception('(s*,t*)-flow of '.$flow.' has to equal sumBalance '.$sumBalance);
        }
        
        $residual = new AlgorithmResidualGraph($resultGraph);
        
        // ...
        
        //initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten
        
        $edges = $resultGraph->getEdges();                                        //initial flow of edges
        
        
        //todo !
        //    

        
        $superSink->destroy();
        $superSource->destroy();
        
        return $resultGraph;
    }
    
}