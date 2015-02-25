<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Algorithm\TravelingSalesmanProblem\MinimumSpanningTree as AlgorithmTspMst;

class Bruteforce implements Base
{
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
     * @var boolean
     */
    private $branchAndBound = true;

    public function createResult(Vertex $startVertex)
    {
        return new ResultFromEdges($startVertex, $this->getEdges($startVertex));
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

    public function setUpperLimitMst(Graph $graph)
    {
        $alg = new AlgorithmTspMst();
        $limit = $alg->createResult($graph->getVertices()->getVertexFirst())->getWeight();

        return $this->setUpperLimit($limit);
    }

    /**
     * turn branch and bound on or off
     *
     * Branch and bound vastly improves this bruteforce algorithm by skipping
     * branches that exceed the best known result. So it's highly recommended
     * to not turn this off. However, this does not work when dealing with
     * negative edge weights, as negative weights might make an initially
     * expensive branch much cheaper in following iterations.
     *
     * @param boolean $toggle
     * @return self $this (chainable)
     */
    public function setBranchAndBound($toggle)
    {
        $this->branchAndBound = !! $toggle;

        return $this;
    }

    /**
     * get resulting (first) best circle of edges connecting all vertices
     *
     * @throws Exception on error
     * @return Edges
     */
    private function getEdges(Vertex $startVertex)
    {
        $this->numEdges = count($startVertex->getGraph()->getVertices());
        if ($this->numEdges < 3) {
            throw new UnderflowException('Needs at least 3 vertices');
        }

        // numEdges 3-12 should work

        $this->bestWeight = $this->upperLimit;
        $this->startVertex = $startVertex;

        $result = $this->step($this->startVertex,
                              0,
                              array(),
                              array()
                  );

        if ($result === NULL) {
            throw new UnderflowException('No resulting solution for TSP found, make sure the graph is complete');
        }

        return new Edges($result);
    }

    /**
     *
     * @param  Vertex    $vertex          current point-of-view
     * @param  number    $totalWeight     total weight (so far)
     * @param  boolean[] $visitedVertices
     * @param  Edge[]    $visitedEdges
     * @return Edge[]
     */
    private function step(Vertex $vertex, $totalWeight, array $visitedVertices, array $visitedEdges)
    {
        // stop recursion if best result is exceeded (branch and bound)
        if ($this->branchAndBound && $this->bestWeight !== NULL && $totalWeight > $this->bestWeight) {
            return NULL;
        }
        // kreis geschlossen am Ende
        if ($vertex === $this->startVertex && count($visitedEdges) === $this->numEdges) {
            // new best result
            $this->bestWeight = $totalWeight;

            return $visitedEdges;
        }

        // only visit each vertex once
        if (isset($visitedVertices[$vertex->getId()])) {
            return NULL;
        }
        $visitedVertices[$vertex->getId()] = true;

        $bestResult = NULL;

        // weiter verzweigen in alle vertices
        foreach ($vertex->getEdgesOut() as $edge) {
            // get target vertex of this edge
            $target = $edge->getVertexToFrom($vertex);

            $weight = $edge->getWeight();
            if ($weight < 0 && $this->branchAndBound) {
                throw new UnexpectedValueException('Edge with negative weight "' . $weight . '" not supported');
            }

            $result = $this->step($target,
                                  $totalWeight + $weight,
                                  $visitedVertices,
                                  array_merge($visitedEdges, array($edge))
                      );

            // new result found
            if ($result !== NULL) {
                // branch and bound enabled (default): returned result MUST be the new best result
                if($this->branchAndBound ||
                   // this is the first result, just use it anyway
                   $bestResult === NULL ||
                   // this is the new best result
                   $this->sumEdges($result) < $this->sumEdges($bestResult)){
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
