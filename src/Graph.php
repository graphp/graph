<?php

namespace Graphp\Graph;

use Graphp\Graph\Attribute\AttributeAware;
use Graphp\Graph\Attribute\AttributeBagReference;
use Graphp\Graph\Exception\InvalidArgumentException;
use Graphp\Graph\Exception\OutOfBoundsException;
use Graphp\Graph\Exception\OverflowException;
use Graphp\Graph\Set\DualAggregate;
use Graphp\Graph\Set\Edges;
use Graphp\Graph\Set\Vertices;
use Graphp\Graph\Set\VerticesMap;

class Graph implements DualAggregate, AttributeAware
{
    protected $verticesStorage = array();
    protected $vertices;

    protected $edgesStorage = array();
    protected $edges;

    protected $attributes = array();

    public function __construct()
    {
        $this->vertices = VerticesMap::factoryArrayReference($this->verticesStorage);
        $this->edges = Edges::factoryArrayReference($this->edgesStorage);
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
     * @param  int|NULL                 $id              new vertex ID to use (defaults to NULL: use next free numeric ID)
     * @param  bool                     $returnDuplicate normal operation is to throw an exception if given id already exists. pass true to return original vertex instead
     * @return Vertex                   (chainable)
     * @throws InvalidArgumentException if given vertex $id is invalid
     * @throws OverflowException        if given vertex $id already exists and $returnDuplicate is not set
     * @uses Vertex::getId()
     */
    public function createVertex($id = NULL, $returnDuplicate = false)
    {
        // no ID given
        if ($id === NULL) {
            $id = $this->getNextId();
        }
        if ($returnDuplicate && $this->vertices->hasVertexId($id)) {
            return $this->vertices->getVertexId($id);
        }

        return new Vertex($this, $id);
    }

    /**
     * Creates a new undirected (bidirectional) edge between the given two vertices.
     *
     * @param  Vertex                   $a
     * @param  Vertex                   $b
     * @return EdgeUndirected
     * @throws InvalidArgumentException
     */
    public function createEdgeUndirected(Vertex $a, Vertex $b)
    {
        if ($a->getGraph() !== $this) {
            throw new InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeUndirected($a, $b);
    }

    /**
     * Creates a new directed edge from the given start vertex to given target vertex
     *
     * @param  Vertex                   $source source vertex
     * @param  Vertex                   $target target vertex
     * @return EdgeDirected
     * @throws InvalidArgumentException
     */
    public function createEdgeDirected(Vertex $source, Vertex $target)
    {
        if ($source->getGraph() !== $this) {
            throw new InvalidArgumentException('Vertices have to be within this graph');
        }

        return new EdgeDirected($source, $target);
    }

    /**
     * create the given number of vertices or given array of Vertex IDs
     *
     * @param  int|array $n number of vertices to create or array of Vertex IDs to create
     * @return Vertices set of Vertices created
     * @uses Graph::getNextId()
     */
    public function createVertices($n)
    {
        $vertices = array();
        if (is_int($n) && $n >= 0) {
            for ($id = $this->getNextId(), $n += $id; $id < $n; ++$id) {
                $vertices[$id] = new Vertex($this, $id);
            }
        } elseif (is_array($n)) {
            // array given => check to make sure all given IDs are available (atomic operation)
            foreach ($n as $id) {
                if (!is_int($id) && !is_string($id)) {
                    throw new InvalidArgumentException('All Vertex IDs have to be of type integer or string');
                } elseif ($this->vertices->hasVertexId($id)) {
                    throw new OverflowException('Given array of Vertex IDs contains an ID that already exists. Given IDs must be unique');
                } elseif (isset($vertices[$id])) {
                    throw new InvalidArgumentException('Given array of Vertex IDs contain duplicate IDs. Given IDs must be unique');
                }

                // temporary marker to check for duplicate IDs in the array
                $vertices[$id] = false;
            }

            // actually create all requested vertices
            foreach ($n as $id) {
                $vertices[$id] = new Vertex($this, $id);
            }
        } else {
            throw new InvalidArgumentException('Invalid number of vertices given. Must be non-negative integer or an array of Vertex IDs');
        }

        return new Vertices($vertices);
    }

    /**
     * get next free/unused/available vertex ID
     *
     * its guaranteed there's NO other vertex with a greater ID
     *
     * @return int
     */
    private function getNextId()
    {
        if (!$this->verticesStorage) {
            return 0;
        }

        // auto ID
        return max(array_keys($this->verticesStorage))+1;
    }

    /**
     * returns the Vertex with identifier $id
     *
     * @param  int|string           $id identifier of Vertex
     * @return Vertex
     * @throws OutOfBoundsException if given vertex ID does not exist
     */
    public function getVertex($id)
    {
        return $this->vertices->getVertexId($id);
    }

    /**
     * checks whether given vertex ID exists in this graph
     *
     * @param int|string $id identifier of Vertex
     * @return bool
     */
    public function hasVertex($id)
    {
        return $this->vertices->hasVertexId($id);
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
        if (isset($this->verticesStorage[$vertex->getId()])) {
            throw new OverflowException('ID must be unique');
        }
        $this->verticesStorage[$vertex->getId()] = $vertex;
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
        $this->vertices = VerticesMap::factoryArrayReference($this->verticesStorage);

        $edges = $this->edgesStorage;
        $this->edgesStorage = array();
        $this->edges = Edges::factoryArrayReference($this->edgesStorage);

        $map = array();
        foreach ($vertices as $originalVertex) {
            assert($originalVertex instanceof Vertex);

            $vertex = new Vertex($this, $originalVertex->getId());
            $vertex->getAttributeBag()->setAttributes($originalVertex->getAttributeBag()->getAttributes());

            // create map with old vertex hash to new vertex object
            $map[spl_object_hash($originalVertex)] = $vertex;
        }

        foreach ($edges as $originalEdge) {
            assert($originalEdge instanceof Edge);

            // use map to match old vertex hashes to new vertex objects
            $vertices = $originalEdge->getVertices()->getVector();
            $v1 = $map[spl_object_hash($vertices[0])];
            $v2 = $map[spl_object_hash($vertices[1])];

            // recreate edge and assign attributes
            if ($originalEdge instanceof EdgeUndirected) {
                $edge = $this->createEdgeUndirected($v1, $v2);
            } else {
                $edge = $this->createEdgeDirected($v1, $v2);
            }
            $edge->getAttributeBag()->setAttributes($originalEdge->getAttributeBag()->getAttributes());
        }
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }

    public function getAttributeBag()
    {
        return new AttributeBagReference($this->attributes);
    }
}
