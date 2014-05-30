<?php

namespace Fhaculty\Graph\Algorithm\StronglyConnectedComponents;

use Fhaculty\Graph\Algorithm\BaseGraph;
use Fhaculty\Graph\Algorithm\Directed;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\VerticeDataMap;

/**
 * @see http://github.com/Trismegiste/Mondrian/blob/master/Graph/Tarjan.php
 */
class Tarjan extends BaseGraph
{
    private $stack;
    private $index;
    private $partition;

    private $indexMap = array();
    private $lowLinkMap = array();

    /**
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        parent::__construct($graph);
        $this->indexMap = new VerticeDataMap();
        $this->lowLinkMap = new VerticeDataMap();
    }

    /**
     * Get the strongly connected components of this digraph by
     * the Tarjan algorithm.
     *
     * Starting from :
     * http://en.wikipedia.org/wiki/Tarjan%27s_strongly_connected_components_algorithm
     *
     * Corrected with the help :
     * https://code.google.com/p/jbpt/source/browse/trunk/jbpt-core/src/main/java/org/jbpt/algo/graph/StronglyConnectedComponents.java
     * @throws \Fhaculty\Graph\Exception\InvalidArgumentException
     * @return array the partition of this graph : an array of an array of vertices
     */
    public function getStronglyConnected()
    {

        // check is directed
        $directed = new Directed($this->graph);
        if($directed->hasUndirected()){
            throw new InvalidArgumentException('Graph shall be directed');
        }

        $this->stack = array();
        $this->index = 0;
        $this->partition = array();

        foreach ($this->graph->getVertices()->getList() as $vertex) {
            if (! isset($this->indexMap[$vertex])) {
                $this->recursivStrongConnect($vertex);
            }
        }

        return $this->partition;
    }

    private function recursivStrongConnect(Vertex $v)
    {
        $this->indexMap[$v] = $this->index;
        $this->lowLinkMap[$v] = $this->index;
        $this->index++;
        array_push($this->stack, $v);

        // Consider successors of v
        foreach ($v->getVerticesEdgeTo() as $w) {
            if (! isset($this->indexMap[$w]) ) {
                // Successor w has not yet been visited; recurse on it
                $this->recursivStrongConnect($w);
                $this->lowLinkMap[$v] = min(array($this->lowLinkMap[$v], $this->lowLinkMap[$w]));
            } elseif (in_array($w, $this->stack)) {
                // Successor w is in stack S and hence in the current SCC
                $this->lowLinkMap[$v] = min(array($this->lowLinkMap[$v], $this->indexMap[$w]));
            }
        }
        // If v is a root node, pop the stack and generate an SCC
        if ($this->lowLinkMap[$v] === $this->indexMap[$v]) {
            $scc = array();
            do {
                $w = array_pop($this->stack);
                array_push($scc, $w);
            } while ($w !== $v);

            if (count($scc)) {
                $this->partition[] = new Vertices($scc);
            }
        }
    }
} 