<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Kruskal as MstKruskal;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchDepthFirst;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\UnexpectedValueException;

class MinimumSpanningTree implements Base
{
    public function createResult(Vertex $startVertex)
    {
        $edges = $this->getEdges($startVertex->getGraph());
        $startVertex = $edges->getEdgeFirst()->getVertices()->getVertexFirst();

        return new ResultFromEdges($startVertex, $edges);
    }

    /**
     *
     * @return Edges
     */
    private function getEdges(Graph $inputGraph)
    {
        $returnEdges = array();

        // Create minimum spanning tree
        $minimumSpanningTreeAlgorithm = new MstKruskal($inputGraph);
        try {
            $minimumSpanningTree = $minimumSpanningTreeAlgorithm->createGraph();
        }
        catch (UnexpectedValueException $ex) {
            throw new UnderflowException('Graph is not connected and therefor not complete', 0, $ex);
        }

        $alg = new SearchDepthFirst($minimumSpanningTree->getVertices()->getVertexFirst());
        // Depth first search in minmum spanning tree (for the eulerian path)

        $startVertex = NULL;
        $oldVertex = NULL;

        // connect vertices in order of the depth first search
        foreach ($alg->getVertices() as $vertex) {

            // get vertex from the original graph (not from the depth first search)
            $vertex = $inputGraph->getVertex($vertex->getId());
                                                                                // need to clone the edge from the original graph, therefore i need the original edge
            if ($startVertex === NULL) {
                $startVertex = $vertex;
            } else {
                // get edge(s) to clone, multiple edges are possible (returns an array if undirected edge)
                $returnEdges []= $oldVertex->getEdgesTo($vertex)->getEdgeFirst();
            }

            $oldVertex = $vertex;
        }

        // connect last vertex with start vertex
        // multiple edges are possible (returns an array if undirected edge)
        $returnEdges []= $oldVertex->getEdgesTo($startVertex)->getEdgeFirst();

        return new Edges($returnEdges);
    }
}
