<?php

namespace Graphp\Graph;

class Graph extends Entity
{
    protected $vertices = array();
    protected $edges = array();

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    /**
     * return list of all vertices added to this graph
     *
     * @psalm-return list<Vertex>
     * @return Vertex[]
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * return list of all edges added to this graph
     *
     * @psalm-return list<Edge>
     * @return Edge[]
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * create a new Vertex in the Graph
     *
     * @param  array $attributes
     * @return Vertex
     */
    public function createVertex(array $attributes = array())
    {
        return new Vertex($this, $attributes);
    }

    /**
     * Creates a new undirected (bidirectional) edge between the given two vertices.
     *
     * @param  Vertex                    $a
     * @param  Vertex                    $b
     * @param  array                     $attributes
     * @return EdgeUndirected
     * @throws \InvalidArgumentException
     */
    public function createEdgeUndirected(Vertex $a, Vertex $b, array $attributes = array())
    {
        if ($a->getGraph() !== $this) {
            throw new \InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeUndirected($a, $b, $attributes);
    }

    /**
     * Creates a new directed edge from the given start vertex to given target vertex
     *
     * @param  Vertex                    $source     source vertex
     * @param  Vertex                    $target     target vertex
     * @param  array                     $attributes
     * @return EdgeDirected
     * @throws \InvalidArgumentException
     */
    public function createEdgeDirected(Vertex $source, Vertex $target, array $attributes = array())
    {
        if ($source->getGraph() !== $this) {
            throw new \InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeDirected($source, $target, $attributes);
    }

    /**
     * Returns a copy of this graph without the given vertex
     *
     * If this vertex was not found in this graph, the returned graph will be
     * identical.
     *
     * @param Vertex $vertex
     * @return self
     */
    public function withoutVertex(Vertex $vertex)
    {
        return $this->withoutVertices(array($vertex));
    }

    /**
     * Returns a copy of this graph without the given vertices
     *
     * If any of the given vertices can not be found in this graph, they will
     * silently be ignored. If neither of the vertices can be found in this graph,
     * the returned graph will be identical.
     *
     * @param Vertex[] $vertices
     * @return self
     */
    public function withoutVertices(array $vertices)
    {
        // keep copy of original vertices and edges and temporarily remove all $vertices and their adjacent edges
        $originalEdges = $this->edges;
        $originalVertices = $this->vertices;
        foreach ($vertices as $vertex) {
            if (($key = \array_search($vertex, $this->vertices, true)) !== false) {
                unset($this->vertices[$key]);
                foreach ($vertex->getEdges() as $edge) {
                    if (($key = \array_search($edge, $this->edges, true)) !== false) {
                        unset($this->edges[$key]);
                    }
                }
            }
        }

        // no vertices matched => return graph as-is
        if (\count($this->vertices) === \count($originalVertices)) {
            return $this;
        }

        // clone graph with vertices/edges temporarily removed, then restore
        $clone = clone $this;
        $this->edges = $originalEdges;
        $this->vertices = $originalVertices;

        return $clone;
    }

    /**
     * Returns a copy of this graph without the given edge
     *
     * If this edge was not found in this graph, the returned graph will be
     * identical.
     *
     * @param Edge $edge
     * @return self
     */
    public function withoutEdge(Edge $edge)
    {
        return $this->withoutEdges(array($edge));
    }

    /**
     * Returns a copy of this graph without the given edges
     *
     * If any of the given edges can not be found in this graph, they will
     * silently be ignored. If neither of the edges can be found in this graph,
     * the returned graph will be identical.
     *
     * @param Edge[] $edges
     * @return self
     */
    public function withoutEdges(array $edges)
    {
        // keep copy of original edges and temporarily remove all $edges
        $original = $this->edges;
        foreach ($edges as $edge) {
            if (($key = \array_search($edge, $this->edges, true)) !== false) {
                unset($this->edges[$key]);
            }
        }

        // no edges matched => return graph as-is
        if (\count($this->edges) === \count($original)) {
            return $this;
        }

        // clone graph with edges temporarily removed, then restore
        $clone = clone $this;
        $this->edges = $original;

        return $clone;
    }

    /**
     * adds a new Vertex to the Graph (MUST NOT be called manually!)
     *
     * @param  Vertex $vertex instance of the new Vertex
     * @return void
     * @internal
     * @see self::createVertex() instead!
     */
    public function addVertex(Vertex $vertex)
    {
        $this->vertices[] = $vertex;
    }

    /**
     * adds a new Edge to the Graph (MUST NOT be called manually!)
     *
     * @param  Edge $edge instance of the new Edge
     * @return void
     * @internal
     * @see Graph::createEdgeUndirected() instead!
     */
    public function addEdge(Edge $edge)
    {
        $this->edges []= $edge;
    }

    /**
     * create new clone/copy of this graph - copy all attributes, vertices and edges
     */
    public function __clone()
    {
        $vertices = $this->vertices;
        $this->vertices = array();

        $edges = $this->edges;
        $this->edges = array();

        $map = array();
        foreach ($vertices as $originalVertex) {
            \assert($originalVertex instanceof Vertex);

            $vertex = new Vertex($this, $originalVertex->getAttributes());

            // create map with old vertex hash to new vertex object
            $map[\spl_object_hash($originalVertex)] = $vertex;
        }

        foreach ($edges as $originalEdge) {
            \assert($originalEdge instanceof Edge);

            // use map to match old vertex hashes to new vertex objects
            $vertices = $originalEdge->getVertices();
            $v1 = $map[\spl_object_hash($vertices[0])];
            $v2 = $map[\spl_object_hash($vertices[1])];

            // recreate edge and assign attributes
            if ($originalEdge instanceof EdgeUndirected) {
                $this->createEdgeUndirected($v1, $v2, $originalEdge->getAttributes());
            } else {
                $this->createEdgeDirected($v1, $v2, $originalEdge->getAttributes());
            }
        }
    }
}
