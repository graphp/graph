<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\MinimumSpanningTree as AlgorithmTspMst;

class Bruteforce extends Base
{
    /**
     *
     * @var Graph
     */
    private $graph;

    /**
     * best weight so for (used for branch-and-bound)
     *
     * @var number|NULL
     */
    private $bestWeight;

    /**
     * reference to start vertex
     *
     * @var Vertex
     */
    private $startVertex;

    /**
     * total number of edges needed
     *
     * @var int
     */
    private $numEdges;

    /**
     * upper limit to use for branch-and-bound (BNB)
     *
     * @var float|NULL
     * @see AlgorithmTspBruteforce::setUpperLimit()
     */
    private $upperLimit = NULL;

    /**
     * whether to use branch-and-bound
     *
     * simply put, there's no valid reason why anybody would want to turn off this flag
     *
     * @var boolean
     */
    private $branchAndBound = true;

    /**
     *
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * explicitly set upper limit to use for branch-and-bound
     *
     * this method can be used to optimize the algorithm by providing an upper
     * bound of when to stop branching any further.
     *
     * @param  double                 $limit
     * @return AlgorithmTspBruteforce $this (chainable)
     */
    public function setUpperLimit($limit)
    {
        $this->upperLimit = $limit;

        return $this;
    }

    public function setUpperLimitMst()
    {
        $alg = new AlgorithmTspMst($this->graph);
        $limit = $alg->createGraph()->getWeight();

        return $this->setUpperLimit($limit);
    }

    protected function getVertexStart()
    {
        return $this->graph->getVertexFirst();
    }

    protected function getGraph()
    {
        return $this->graph;
    }

    /**
     * get resulting (first) best circle of edges connecting all vertices
     *
     * @throws Exception on error
     * @return Edge[]
     */
    public function getEdges()
    {
        $this->numEdges = $this->graph->getNumberOfVertices();
        if ($this->numEdges < 3) {
            throw new UnderflowException('Needs at least 3 vertices');
        }

        // numEdges 3-12 should work

        $this->bestWeight = $this->upperLimit;
        $this->startVertex = $this->graph->getVertexFirst(); // actual start doesn't really matter as we're only considering complete graphs here

        $result = $this->step($this->startVertex,
                              0,
                              array(),
                              array()
                  );

        if ($result === NULL) {
            throw new UnexpectedValueException('No resulting solution for TSP found');
        }

        return $result;
    }

    /**
     *
     * @param  Vertex    $vertex          current point-of-view
     * @param  number    $totalWeight     total weight (so far)
     * @param  boolean[] $visitedVertices
     * @param  Edge[]    $visitedEdges
     * @return Edge[]
     */
    private function step(Vertex $vertex,$totalWeight,array $visitedVertices,array $visitedEdges)
    {
        if ($this->branchAndBound && $this->bestWeight !== NULL && $totalWeight >= $this->bestWeight) { // stop recursion if best result is exceeded (branch and bound)

            return NULL;
        }
        if ($vertex === $this->startVertex && count($visitedEdges) === $this->numEdges) { // kreis geschlossen am Ende
            $this->bestWeight = $totalWeight; // new best result

            return $visitedEdges;
        }

        if (isset($visitedVertices[$vertex->getId()])) {                          // only visit each vertex once

            return NULL;
        }
        $visitedVertices[$vertex->getId()] = true;

        $bestResult = NULL;

        foreach ($vertex->getEdgesOut() as $edge) {                          // weiter verzweigen in alle vertices
            $target = $edge->getVertexToFrom($vertex);                          // get target vertex of this edge

            $weight = $edge->getWeight();
            if ($weight < 0) {
                throw new UnexpectedValueException('Edge with negative weight "'.$weight.'" not supported');
            }

            $result = $this->step($target,
                                  $totalWeight + $weight,
                                  $visitedVertices,
                                  array_merge($visitedEdges,array($edge))
                      );

            if ($result !== NULL) { // new result found
                if($this->branchAndBound || // branch and bound enabled (default): returned result MUST be the new best result
                   $bestResult === NULL || // this is the first result, just use it anyway
                   $this->sumEdges($result) < $this->sumEdges($bestResult)){ // this is the new best result
                    $bestResult = $result;
                }
            }
        }

        return $bestResult;
    }

    /**
     * get sum of weight of given edges
     *
     * no need to optimize this further, as it's only evaluated if branchAndBound is disabled and
     * there's no valid reason why anybody would want to do so.
     *
     * @param  Edge[] $edges
     * @return float
     */
    private function sumEdges(array $edges)
    {
        $sum = 0;
        foreach ($edges as $edge) {
            $sum += $edge->getWeight();
        }

        return $sum;
    }
}
