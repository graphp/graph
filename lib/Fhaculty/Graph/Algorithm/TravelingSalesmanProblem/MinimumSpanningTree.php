<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Kruskal as MstKruskal;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchDepthFirst;

class MinimumSpanningTree
{
    public function createResult(Graph $inputGraph)
    {
        return new ResultFromEdges($inputGraph->getVertices()->getVertexFirst(), $this->getEdges($inputGraph));
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
        $minimumSpanningTree = $minimumSpanningTreeAlgorithm->createGraph();

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
