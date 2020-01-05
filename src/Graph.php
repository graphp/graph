<?php

namespace Graphp\Graph;

use Graphp\Graph\Exception\InvalidArgumentException;
use Graphp\Graph\Exception\OutOfBoundsException;
use Graphp\Graph\Set\DualAggregate;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\Vertices;

class Graph extends Entity implements DualAggregate
{
    protected $verticesStorage = array();
    protected $vertices;

    protected $edgesStorage = array();
    protected $edges;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->vertices = Vertices::factoryArrayReference($this->verticesStorage);
        $this->edges = Edges::factoryArrayReference($this->edgesStorage);
        $this->attributes = $attributes;
    }

    /**
     * return set of Vertices added to this graph
     *
     * @return Vertices
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * return set of ALL Edges added to this graph
     *
     * @return Edges
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
     * @param  Vertex                   $a
     * @param  Vertex                   $b
     * @param  array                    $attributes
     * @return EdgeUndirected
     * @throws InvalidArgumentException
     */
    public function createEdgeUndirected(Vertex $a, Vertex $b, array $attributes = array())
    {
        if ($a->getGraph() !== $this) {
            throw new InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeUndirected($a, $b, $attributes);
    }

    /**
     * Creates a new directed edge from the given start vertex to given target vertex
     *
     * @param  Vertex                   $source     source vertex
     * @param  Vertex                   $target     target vertex
     * @param  array                    $attributes
     * @return EdgeDirected
     * @throws InvalidArgumentException
     */
    public function createEdgeDirected(Vertex $source, Vertex $target, array $attributes = array())
    {
        if ($source->getGraph() !== $this) {
            throw new InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeDirected($source, $target, $attributes);
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
        $this->verticesStorage[] = $vertex;
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
        $this->edgesStorage []= $edge;
    }

    /**
     * remove the given edge from list of connected edges (MUST NOT be called manually!)
     *
     * @param  Edge                     $edge
     * @return void
     * @throws InvalidArgumentException if given edge does not exist (should not ever happen)
     * @internal
     * @see Edge::destroy() instead!
     */
    public function removeEdge(Edge $edge)
    {
        try {
            unset($this->edgesStorage[$this->edges->getIndexEdge($edge)]);
        }
        catch (OutOfBoundsException $e) {
            throw new InvalidArgumentException('Invalid Edge does not exist in this Graph');
        }
    }

    /**
     * remove the given vertex from list of known vertices (MUST NOT be called manually!)
     *
     * @param  Vertex                   $vertex
     * @return void
     * @throws InvalidArgumentException if given vertex does not exist (should not ever happen)
     * @internal
     * @see Vertex::destroy() instead!
     */
    public function removeVertex(Vertex $vertex)
    {
        try {
            unset($this->verticesStorage[$this->vertices->getIndexVertex($vertex)]);
        }
        catch (OutOfBoundsException $e) {
            throw new InvalidArgumentException('Invalid Vertex does not exist in this Graph');
        }
    }

    /**
     * create new clone/copy of this graph - copy all attributes, vertices and edges
     */
    public function __clone()
    {
        $vertices = $this->verticesStorage;
        $this->verticesStorage = array();
        $this->vertices = Vertices::factoryArrayReference($this->verticesStorage);

        $edges = $this->edgesStorage;
        $this->edgesStorage = array();
        $this->edges = Edges::factoryArrayReference($this->edgesStorage);

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
            $vertices = $originalEdge->getVertices()->getVector();
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
