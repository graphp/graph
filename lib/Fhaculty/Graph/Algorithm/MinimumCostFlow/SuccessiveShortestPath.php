<?php

namespace Fhaculty\Graph\Algorithm\MinimumCostFlow;

use Fhaculty\Graph\Exception\DomainException;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Algorithm\ShortestPath\MooreBellmanFord as SpMooreBellmanFord;
use Fhaculty\Graph\Algorithm\ResidualGraph;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchBreadthFirst;

class SuccessiveShortestPath extends Base
{
    /**
     * @uses Graph::createGraphClone()
     * @uses AlgorithmResidualGraph::createGraph()
     * @uses AlgorithmSpMooreBellmanFord::getEdgesTo(Vertex $targetVertex)
     *
     * @see AlgorithmMCF::createGraph()
     */
    public function createGraph()
    {
        $this->checkBalance();
        $resultGraph = $this->graph->createGraphClone();

        // initial balance to 0
        $vertices = $resultGraph->getVertices();
        foreach ($vertices as $vertex) {
            $vertex->setBalance(0);
        }

        // initial flow of edges
        $edges = $resultGraph->getEdges();
        foreach ($edges as $edge) {
            // 0 if weight of edge is positiv
            $flow = 0;

            // maximal flow if weight of edge is negative
            if ($edge->getWeight() < 0) {
                $flow = $edge->getCapacity();

                if ($edge instanceof EdgeDirected) {
                    $startVertex = $edge->getVertexStart();
                    $endVertex = $edge->getVertexEnd();

                    // add balance to start- and end-vertex
                    $this->addBalance($startVertex, $flow);
                    $this->addBalance($endVertex, - $flow);
                } else {
                    throw new UnexpectedValueException('Undirected Edges not suported');
                }
            }

            $edge->setFlow($flow);
        }

        // return or Exception insite this while
        while (true) {
            // create residual graph
            $algRG = new ResidualGraph($resultGraph);
            $residualGraph = $algRG->createGraph();

            // search for a source
            try {
                $sourceVertex = $this->getVertexSource($residualGraph);
            // if no source is found the minimum-cost flow is found
            } catch (UnderflowException $ignore) {
                break;
            }

            // search for reachble sink from this source
            try {
                $targetVertex = $this->getVertexSink($sourceVertex);
            // if no target is found the network has not enough capacity
            } catch (UnderflowException $ignore) {
                throw new UnexpectedValueException('The graph has not enough capacity for the minimum-cost flow');
            }

            // calculate shortest path between source- and target-vertex
            $algSP = new SpMooreBellmanFord($sourceVertex);
            $edgesOnFlow = $algSP->getEdgesTo($targetVertex);

            // calculate the maximal possible flow
                                                                                // new flow is the maximal possible flow for this path
            $newflow    =    $this->graph->getVertex($sourceVertex->getId())->getBalance() - $sourceVertex->getBalance();
            $targetFlow = - ($this->graph->getVertex($targetVertex->getId())->getBalance() - $targetVertex->getBalance());

            // get minimum of source and target
            if ($targetFlow < $newflow) {
                $newflow = $targetFlow;
            }

            // get minimum of capacity remaining on path
            $minCapacity = $edgesOnFlow->getEdgeOrder(Edges::ORDER_CAPACITY_REMAINING)->getCapacityRemaining();
            if ($minCapacity < $newflow) {
                $newflow = $minCapacity;
            }

            // add the new flow to the path
            $this->addFlow($resultGraph, $edgesOnFlow, $newflow);

            // add balance to source and remove for the sink
            $oriSourceVertex = $resultGraph->getVertex($sourceVertex->getId());
            $oriTargetVertex = $resultGraph->getVertex($targetVertex->getId());

            $this->addBalance($oriSourceVertex, $newflow);
            $this->addBalance($oriTargetVertex, - $newflow);
        }

        return $resultGraph;
    }

    /**
     * check if balance on each vertex of the given graph matches the original graph's
     *
     * @param  Graph     $graph
     * @return boolean
     * @throws Exception if given graph is not a clone of the original graph (each vertex has to be present in both graphs)
     * @uses Graph::getBalanace()
     * @uses Graph::getVertex()
     */
    private function isBalanceReached(Graph $graph)
    {
        if (count($graph->getVertices()) !== count($this->graph->getVertices())) {
            throw new DomainException('Given graph does not appear to be a clone of input graph');
        }
        foreach ($this->graph->getVertices()->getMap() as $vid => $vertex) {
            if ($vertex->getBalance() !== $graph->getVertex($vid)->getBalance()) {
                return false;
            }
        }

        return true;
    }

    /**
     *
     *
     * @param  Graph     $graph
     * @throws Exception if there is no left source vertex
     *
     * @return Vertex a source vertex in the given graph
     */
    private function getVertexSource(Graph $graph)
    {
        foreach ($graph->getVertices()->getMap() as $vid => $vertex) {
            if ($this->graph->getVertex($vid)->getBalance() - $vertex->getBalance() > 0) {
                return $vertex;
            }
        }
        throw new UnderflowException('No source vertex found in graph');
    }

    /**
     *
     *
     * @param  Vertex    $source
     * @throws Exception if there is no reachable sink vertex
     *
     * @return Vertex a sink-vertex that is reachable from the source
     * @uses BreadthFirst::getVertices()
     */
    private function getVertexSink(Vertex $source)
    {
        // search for reachable Vertices
        $algBFS = new SearchBreadthFirst($source);

        foreach ($algBFS->getVertices()->getMap() as $vid => $vertex) {
            if ($this->graph->getVertex($vid)->getBalance() - $vertex->getBalance() < 0) {
                return $vertex;
            }
        }
        throw new UnderflowException('No sink vertex connected to given source vertex found');
    }

    private function addBalance(Vertex $vertex, $balance)
    {
        $vertex->setBalance($vertex->getBalance() + $balance);
    }
}
