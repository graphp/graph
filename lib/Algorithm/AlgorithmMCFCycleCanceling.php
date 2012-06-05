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
        $algMaxFlow = new AlgorithmMaxFlowEdmondsKarp($superSource,$superSink);
        $flow = $algMaxFlow->getMaxFlowValue();

        //visualize($resultGraph);
        //visualize($alg->createGraph());

        if($flow !== $sumBalance){
            throw new Exception('(s*,t*)-flow of '.$flow.' has to equal sumBalance '.$sumBalance);
        }

        $returnGraph = $algMaxFlow->createGraph();

        try {
            while(true){
                // Find negative cycle
                $algCycle = new AlgorithmDetectNegativeCycle($returnGraph, $vertex);
                $negativeCycle = $algCycle->getNegativeCycle();

                $edgeFromFlowPath = Edge::getFirst($negativeCycle->getEdges(),Edge::ORDER_CAPACITY);
                $newFlowValue = $edgeFromFlowPath->getCapacity();

                // Add negative cycle as flow to $returnGraph
                foreach ($negativeCycle->getEdges() as $edge){
                    $originalEdge = $returnGraph->getEdgeClone($edge);
                    $originalEdge->setFlow($originalEdge->getFlow() + $newFlowValue);
                }

                $returnGraph = new AlgorithmResidualGraph($returnGraph);
            }
        }
        catch (Exception $ignore){
            // DONE no negative cycle found... continue and return...
        }


        //initial-zustand setzten, 0 für Positiv gewichtete Kanten max für negaitv gewichtete Kanten

        $returnGraph->getVertex($superSink->getId())->destroy();
        $returnGraph->getVertex($superSource->getId())->destroy();
        $superSink->destroy();
        $superSource->destroy();

        //visualize($returnGraph);

        //$returnGraph = $this->getFlowGraphFromResidualGraph($returnGraph);
        return $returnGraph;
    }



    /**
     * Merges a residual graph with initial graph
     *
     * @param $residualGraph
     * @return Graph graph with maximal flow
     */
    private function getFlowGraphFromResidualGraph($residualGraph){

        $resultGraph = $this->graph->createGraphCloneEdgeless();                // Process original graph and create a new graph that contains the flow

        $originalGraphEdgesArray = $this->graph->getEdges();

        // For every edge in the residual graph,
        // that has an inversed edge in the original graph:
        // Insert the inversed residual edge into the new graph
        foreach ($originalGraphEdgesArray as $edge){
            // Inverse the edge
            $residualEdge = $this->getEdgeSimilarFromGraph($edge, $residualGraph, true);

            // Add inversed edge to return graph
            $newFlowEdge = $resultGraph->createEdgeClone($edge);

            // Set flow of the edge
            if($residualEdge != NULL){
                $newFlowEdge->setFlow($residualEdge->getCapacity());
            }
            else{
                $newFlowEdge->setWeight(0);
            }
        }
        return $resultGraph;
    }


    /**
     * Extracts (optional: inversed) edge from the given graph
     *
     * @param Graph $edge
     * @param Graph $newGraph
     * @param Boolean $inverse
     * @return Graph
     */
    private function getEdgeSimilarFromGraph($edge, $newGraph, $inverse=false){
        try{
            return $newGraph->getEdgeClone($edge,$inverse);
        }
        catch(Exception $ignore){
        }
        return NULL;
    }

}