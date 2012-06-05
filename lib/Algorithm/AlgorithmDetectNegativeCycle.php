<?php

class AlgorithmDetectNegativeCycle extends Algorithm{

    /**
     *
     * @var Graph
     */
    private $graph;

    /**
     *
     * @var Vertex
     */
    private $startVertex;

    /**
     *
     * @param Graph $graph
     * @param Vertex $startVertex
     */
    public function __construct(Graph $graph, Vertex $startVertex){
        $this->graph = $graph;
        $this->startVertex = $startVertex;
    }

    //TODO this function
    /**
     * Depth-first-search for a negative cycle
     *
     * @param Vertex $vertex
     * @param array $visitedVertices
     *
     * @return Graph the result graph if a negative cycle is found or NULL
     */
    private function searchNextDepth(Vertex $currentVertex, array $visitedVertices, array $predessesors){

        if ( isset($visitedVertices[$currentVertex->getId()]) ){                       //cycle
            $id = $currentVertex->getId();

            //checke ob negativer cycle
            $cycle = $currentVertex->getGraph()->createGraphCloneEdgeless();
            $weight=0;
            $tempVertex=$currentVertex;
            do{
                $predesssorVertex = $predessesors[$tempVertex->getId()];
                $edges = $predesssorVertex->getEdgesTo($tempVertex);
                $edge = $edges[0];
                $weight = $weight + $edge->getWeight();
                $cycle->createEdgeClone($edge);
                $tempVertex=$predesssorVertex;
            }
            while ($currentVertex !== $predesssorVertex);
            //baue graph zusammen
            //gebe graph zurück
            if($weight<0){
                return $cycle;
            }
            return NULL;
            //else continue with search
        }

        $visitedVertices[$currentVertex->getId()] = true;

        $vertices = $currentVertex->getVerticesEdgeTo();                    //get next level of vertices
        foreach ($vertices as $vertex){                                            //checke for all vertices if the found the negative cycle
            $predessesors[$vertex->getId()]=$currentVertex;
            $graph = $this->searchNextDepth($vertex, $visitedVertices, $predessesors);

            if ($graph != NULL){                                                //If they found the negative cycle return the result graph
                return $graph;
            }
        }

        return NULL;                                                            //otherwise return NULL
    }

 /**
     * Searches backwords for a negative cycle
     *
     * @param Vertex $startVertex optional
     *
     * @return Graph with the negative cycle
     *
     * @throws Exception if there isn't a negative cycle
     */
    public function getNegativeCycleForStartVertex(){

        $visitedVertices = array();
        $predessesors= array();
        $visitedVertices[$this->startVertex->getId()] = true;                        //Visited Vertices with the cost to ???

        $vertices = $this->startVertex->getVerticesEdgeTo();                    //get next level of vertices

        foreach ($vertices as $vertex){                                            //checke for all vertices if the found the negative cycle
            $predessesors[$vertex->getId()] = $this->startVertex;
            $graph = $this->searchNextDepth($vertex, $visitedVertices, $predessesors);

            if ($graph != NULL){                                                //If they found the negative cycle return the result graph
                return $graph;
            }
        }

        throw  new Exception("No negative cycle found");
    }

    /**
     * Searches all vertices for the first negative cycle
     *
     * @return Graph with the negative cycle
     *
     * @throws Exception if there isn't a negative cycle
     */
    public function getNegativeCycle(){

        $visitedVertices = array();
        foreach ($this->graph->getVertices() as $currentVertex){
            $this->startVertex=$currentVertex;
            try {
                return $this->getNegativeCycleForStartVertex();
            } catch (Exception $e) {

            }

        }
        throw  new Exception("No negative cycle found");
    }

}
