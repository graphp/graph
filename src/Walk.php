<?php

namespace Graphp\Graph;

use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\Vertices;
use Graphp\Graph\Set\DualAggregate;

/**
 * Base Walk class
 *
 * The general term "Walk" bundles the following mathematical concepts:
 * walk, path, cycle, circuit, loop, trail, tour, etc.
 *
 * @link http://en.wikipedia.org/wiki/Path_%28graph_theory%29
 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
 * @see Graphp\Graph\Algorithm\Property\WalkProperty for checking special cases, such as cycles, loops, closed trails, etc.
 */
class Walk implements DualAggregate
{
    /**
     * construct new walk from given start vertex and given array of edges
     *
     * @param  Edges  $edges
     * @param  Vertex $startVertex
     * @return Walk
     */
    public static function factoryFromEdges(Edges $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex);
        $vertexCurrent = $startVertex;
        foreach ($edges as $edge) {
            $vertexCurrent = $edge->getVertexToFrom($vertexCurrent);
            $vertices []= $vertexCurrent;
        }

        return new self(new Vertices($vertices), $edges);
    }

    /**
     * create new walk instance between given set of Vertices / array of Vertex instances
     *
     * @param  Vertices                          $vertices
     * @param  null|string|callable(Edge):number $orderBy
     * @param  bool                              $desc
     * @return Walk
     * @throws \UnderflowException               if no vertices were given
     * @see Edges::getEdgeOrder() for parameters $by and $desc
     */
    public static function factoryFromVertices(Vertices $vertices, $orderBy = null, $desc = false)
    {
        $edges = array();
        $last = NULL;
        foreach ($vertices as $vertex) {
            // skip first vertex as last is unknown
            if ($last !== NULL) {
                // pick edge between last vertex and this vertex
                /* @var $last Vertex */
                if ($orderBy === null) {
                    $edges []= $last->getEdgesTo($vertex)->getEdgeFirst();
                } else {
                    $edges []= $last->getEdgesTo($vertex)->getEdgeOrder($orderBy, $desc);
                }
            }
            $last = $vertex;
        }
        if ($last === NULL) {
            throw new \UnderflowException('No vertices given');
        }

        return new self($vertices, new Edges($edges));
    }

    /**
     * create new cycle instance with edges between given vertices
     *
     * @param  Vertices                          $vertices
     * @param  null|string|callable(Edge):number $orderBy
     * @param  bool                              $desc
     * @return Walk
     * @throws \UnderflowException               if no vertices were given
     * @throws \InvalidArgumentException         if vertices do not form a valid cycle
     * @see Edges::getEdgeOrder() for parameters $by and $desc
     * @uses self::factoryFromVertices()
     */
    public static function factoryCycleFromVertices(Vertices $vertices, $orderBy = null, $desc = false)
    {
        $cycle = self::factoryFromVertices($vertices, $orderBy, $desc);

        if ($cycle->getEdges()->isEmpty()) {
            throw new \InvalidArgumentException('Cycle with no edges can not exist');
        }

        if ($cycle->getVertices()->getVertexFirst() !== $cycle->getVertices()->getVertexLast()) {
            throw new \InvalidArgumentException('Cycle has to start and end at the same vertex');
        }

        return $cycle;
    }

    /**
     * create new cycle instance with vertices connected by given edges
     *
     * @param  Edges  $edges
     * @param  Vertex $startVertex
     * @return Walk
     * @throws \InvalidArgumentException if the given array of edges does not represent a valid cycle
     * @uses self::factoryFromEdges()
     */
    public static function factoryCycleFromEdges(Edges $edges, Vertex $startVertex)
    {
        $cycle = self::factoryFromEdges($edges, $startVertex);

        // ensure this walk is actually a cycle by checking start = end
        if ($cycle->getVertices()->getVertexLast() !== $startVertex) {
            throw new \InvalidArgumentException('The given array of edges does not represent a cycle');
        }

        return $cycle;
    }

    /**
     * @var Vertices
     */
    protected $vertices;

    /**
     * @var Edges
     */
    protected $edges;

    protected function __construct(Vertices $vertices, Edges $edges)
    {
        $this->vertices = $vertices;
        $this->edges = $edges;
    }

    /**
     * return original graph
     *
     * @return Graph
     * @uses self::getVertices()
     * @uses Vertices::getVertexFirst()
     * @uses Vertex::getGraph()
     */
    public function getGraph()
    {
        return $this->vertices->getVertexFirst()->getGraph();
    }

    /**
     * return set of all Edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * If you need to return set a of all unique Edges of walk, use
     * `Walk::getEdges()->getEdgesDistinct()` instead.
     *
     * @return Edges
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * return set of all Vertices of walk (in sequence visited in walk, may contain duplicates)
     *
     * If you need to return set a of all unique Vertices of walk, use
     * `Walk::getVertices()->getVerticesDistinct()` instead.
     *
     * If you need to return the source vertex (first vertex of walk), use
     * `Walk::getVertices()->getVertexFirst()` instead.
     *
     * If you need to return the target/destination vertex (last vertex of walk), use
     * `Walk::getVertices()->getVertexLast()` instead.
     *
     * @return Vertices
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * get alternating sequence of vertex, edge, vertex, edge, ..., vertex
     *
     * @return array
     */
    public function getAlternatingSequence()
    {
        $edges    = $this->edges->getVector();
        $vertices = $this->vertices->getVector();

        $ret = array();
        for ($i = 0, $l = \count($this->edges); $i < $l; ++$i) {
            $ret []= $vertices[$i];
            $ret []= $edges[$i];
        }
        $ret[] = $vertices[$i];

        return $ret;
    }
}
