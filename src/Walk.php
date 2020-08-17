<?php

namespace Graphp\Graph;

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
class Walk
{
    /**
     * construct new walk from given start vertex and given array of edges
     *
     * @param  Edge[] $edges
     * @param  Vertex $startVertex
     * @return Walk
     */
    public static function factoryFromEdges(array $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex);
        $vertexCurrent = $startVertex;
        foreach ($edges as $edge) {
            $vertexCurrent = $edge->getVertexToFrom($vertexCurrent);
            $vertices []= $vertexCurrent;
        }

        return new self($vertices, \array_values($edges));
    }

    /**
     * create new walk instance between given array of Vertex instances
     *
     * @param  Vertex[]                          $vertices
     * @param  null|string|callable(Edge):number $orderBy
     * @param  bool                              $desc
     * @return Walk
     * @throws \UnderflowException               if no vertices were given
     * @see self::factoryCycleFromVertices() for parameters $orderBy and $desc
     */
    public static function factoryFromVertices(array $vertices, $orderBy = null, $desc = false)
    {
        $edges = array();
        $last = NULL;
        foreach ($vertices as $vertex) {
            // skip first vertex as last is unknown
            if ($last !== NULL) {
                // pick edge between last vertex and this vertex
                \assert($last instanceof Vertex);
                $edges[] = self::pickEdge($last->getEdgesTo($vertex), $orderBy, $desc);
            }
            $last = $vertex;
        }
        if ($last === NULL) {
            throw new \UnderflowException('No vertices given');
        }

        return new self(\array_values($vertices), $edges);
    }

    /**
     * create new cycle instance with edges between given vertices
     *
     * @param  Vertex[]                          $vertices
     * @param  null|string|callable(Edge):number $orderBy
     * @param  bool                              $desc
     * @return Walk
     * @throws \UnderflowException               if no vertices were given
     * @throws \InvalidArgumentException         if vertices do not form a valid cycle
     * @see self::factoryCycleFromVertices() for parameters $orderBy and $desc
     * @uses self::factoryFromVertices()
     */
    public static function factoryCycleFromVertices(array $vertices, $orderBy = null, $desc = false)
    {
        $cycle = self::factoryFromVertices($vertices, $orderBy, $desc);

        if (!$cycle->getEdges()) {
            throw new \InvalidArgumentException('Cycle with no edges can not exist');
        }

        if (\reset($vertices) !== \end($vertices)) {
            throw new \InvalidArgumentException('Cycle has to start and end at the same vertex');
        }

        return $cycle;
    }

    /**
     * create new cycle instance with vertices connected by given edges
     *
     * @param  Edge[] $edges
     * @param  Vertex $startVertex
     * @return Walk
     * @throws \InvalidArgumentException if the given array of edges does not represent a valid cycle
     * @uses self::factoryFromEdges()
     */
    public static function factoryCycleFromEdges(array $edges, Vertex $startVertex)
    {
        $cycle = self::factoryFromEdges($edges, $startVertex);

        // ensure this walk is actually a cycle by checking start = end
        $vertices = $cycle->getVertices();
        if (\end($vertices) !== $startVertex) {
            throw new \InvalidArgumentException('The given array of edges does not represent a cycle');
        }

        return $cycle;
    }

    /**
     * @param Edge[]                            $edges
     * @param null|string|callable(Edge):number $orderBy
     * @param bool                              $desc
     * @return Edge
     * @throws \UnderflowException
     */
    private static function pickEdge(array $edges, $orderBy, $desc)
    {
        if (!$edges) {
            throw new \UnderflowException('No edges between two vertices found');
        }

        if ($orderBy === null) {
            return \reset($edges);
        }

        if (\is_string($orderBy)) {
            $orderBy = function (Edge $edge) use ($orderBy) {
                return $edge->getAttribute($orderBy);
            };
        }

        $ret = NULL;
        $best = NULL;
        foreach ($edges as $edge) {
            $now = $orderBy($edge);

            if ($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)) {
                $ret = $edge;
                $best = $now;
            }
        }

        return $ret;
    }

    /**
     * @var Vertex[]
     */
    protected $vertices;

    /**
     * @var Edge[]
     */
    protected $edges;

    /**
     * @psalm-param list<Vertex> $vertices
     * @psalm-param list<Edge>   $edges
     * @param Vertex[] $vertices
     * @param Edge[]   $edges
     */
    protected function __construct(array $vertices, array $edges)
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
        $vertex = \reset($this->vertices);
        \assert($vertex instanceof Vertex);

        return $vertex->getGraph();
    }

    /**
     * return list of all edges of walk (in sequence visited in walk, may contain duplicates)
     *
     * @psalm-return list<Edge>
     * @return Edge[]
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * return list of all vertices of walk (in sequence visited in walk, may contain duplicates)
     *
     * If you need to return the source vertex (first vertex of walk), you can
     * use something like this:
     *
     * ```php
     * $vertices = $walk->getVertices();
     * $firstVertex = \reset($vertices);
     * ```
     *
     * If you need to return the target/destination vertex (last vertex of walk),
     * you can use something like this:
     *
     * ```php
     * $vertices = $walk->getVertices();
     * $lastVertex = \end($vertices);
     * ```
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * get alternating sequence of vertex, edge, vertex, edge, ..., vertex
     *
     * @psalm-return list<Vertex|Edge>
     * @return array<int,Vertex|Edge>
     */
    public function getAlternatingSequence()
    {
        $ret = array();
        for ($i = 0, $l = \count($this->edges); $i < $l; ++$i) {
            $ret []= $this->vertices[$i];
            $ret []= $this->edges[$i];
        }
        $ret[] = $this->vertices[$i];

        return $ret;
    }
}
