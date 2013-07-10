<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchBreadthFirst;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\UnderflowException;

class ConnectedComponents extends BaseGraph
{
    /**
     * create subgraph with all vertices connected to given vertex (i.e. the connected component of ths given vertex)
     *
     * @param  Vertex                   $vertex
     * @return Graph
     * @throws InvalidArgumentException if given vertex is not from same graph
     * @uses AlgorithmSearchBreadthFirst::getVerticesIds()
     * @uses Graph::createGraphCloneVertices()
     */
    public function createGraphComponentVertex(Vertex $vertex)
    {
        if ($vertex->getGraph() !== $this->graph) {
            throw new InvalidArgumentException('This graph does not contain the given vertex');
        }

        return $this->graph->createGraphCloneVertices($this->createSearch($vertex)->getVertices());
    }

    private function createSearch(Vertex $vertex)
    {
        $alg = new SearchBreadthFirst($vertex);

        // follow into both directions (loosely connected)
        return $alg->setDirection(SearchBreadthFirst::DIRECTION_BOTH);
    }

    /**
     * check whether this graph consists of only a single component
     *
     * this is faster than calling getNumberOfComponents(), as it only has to
     * count all vertices in one component to see if the graph consists of only
     * a single component
     *
     * @return boolean
     * @uses AlgorithmSearchBreadthFirst::getNumberOfVertices()
     */
    public function isSingle()
    {
        try {
            $vertex = $this->graph->getVertexFirst();
        }
        catch (UnderflowException $e) {
            // no first vertex => empty graph => has zero components
            return false;
        }
        $alg = $this->createSearch($vertex);

        return ($this->graph->getNumberOfVertices() === $alg->getNumberOfVertices());
    }

    /**
     * @return int number of components
     * @uses Graph::getVertices()
     * @uses AlgorithmSearchBreadthFirst::getVerticesIds()
     */
    public function getNumberOfComponents()
    {
        $visitedVertices = array();
        $components = 0;

        // for each vertices
        foreach ($this->graph->getVertices() as $vid => $vertex) {
            // did I visit this vertex before?
            if (!isset($visitedVertices[$vid])) {

                // get all vertices of this component
                $newVertices = $this->createSearch($vertex)->getVerticesIds();

                ++$components;

                // mark the vertices of this component as visited
                foreach ($newVertices as $vid) {
                    $visitedVertices[$vid] = true;
                }
            }
        }

        // return number of components
        return $components;
    }

    /**
     * separate input graph into separate independant and unconnected graphs
     *
     * @return Graph[]
     * @uses Graph::getVertices()
     * @uses AlgorithmSearchBreadthFirst::getVertices()
     */
    public function createGraphsComponents()
    {
        $visitedVertices = array();
        $graphs = array();

        // for each vertices
        foreach ($this->graph->getVertices() as $vid => $vertex) {
            // did I visit this vertex before?
            if (!isset($visitedVertices[$vid])) {

                $alg = $this->createSearch($vertex);
                // get all vertices of this component
                $newVertices = $alg->getVertices();

                // mark the vertices of this component as visited
                foreach ($newVertices as $vid => $unusedVertex) {
                    $visitedVertices[$vid] = true;
                }

                $graphs []= $this->graph->createGraphCloneVertices($newVertices);
            }
        }

        return $graphs;
    }
}
