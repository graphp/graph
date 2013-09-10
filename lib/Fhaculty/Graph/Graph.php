<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exporter\ExporterInterface;
use Fhaculty\Graph\Exporter\Dot;
use Fhaculty\Graph\Exception\BadMethodCallException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\OverflowException;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\RuntimeException;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Algorithm\ConnectedComponents as AlgorithmConnectedComponents;
use Fhaculty\Graph\Algorithm\Bipartit as AlgorithmBipartit;
use Fhaculty\Graph\Algorithm\Eulerian as AlgorithmEulerian;
use Fhaculty\Graph\Algorithm\Groups as AlgorithmGroups;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\VerticesMap;
use Fhaculty\Graph\Set\Edges;

class Graph extends Set
{
    /**
     * @var ExporterInterface|null
     * @see self::setExporter()
     */
    protected $exporter = null;

    protected $verticesStorage = array();
    protected $vertices;

    protected $edgesStorage = array();
    protected $edges;

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
     * @param  boolean                  $returnDuplicate normal operation is to throw an exception if given id already exists. pass true to return original vertex instead
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
        } elseif (!is_int($id) && !is_string($id)) {
            throw new InvalidArgumentException('Vertex ID has to be of type integer or string');
        }
        if ($this->vertices->hasVertexId($id)) {
            if ($returnDuplicate) {
                return $this->vertices->getVertexId($id);
            }
            throw new OverflowException('ID must be unique');
        }
        $vertex = new Vertex($id, $this);
        $this->verticesStorage[$id] = $vertex;

        return $vertex;
    }

    /**
     * create a new Vertex in this Graph from the given input Vertex of another graph
     *
     * @param  Vertex           $originalVertex
     * @return Vertex           new vertex in this graph
     * @throws RuntimeException if vertex with this ID already exists
     */
    public function createVertexClone(Vertex $originalVertex)
    {
        $id = $originalVertex->getId();
        if ($this->vertices->hasVertexId($id)) {
            throw new RuntimeException('Id of cloned vertex already exists');
        }
        $newVertex = new Vertex($id, $this);
        // TODO: properly set attributes of vertex
        $newVertex->setLayout($originalVertex->getLayout());
        $newVertex->setBalance($originalVertex->getBalance());
        $newVertex->setGroup($originalVertex->getGroup());
        $this->verticesStorage[$id] = $newVertex;

        return $newVertex;
    }

    /**
     * create new clone/copy of this graph - copy all attributes and vertices, but do NOT copy edges
     *
     * using this method is faster than creating a new graph and calling createEdgeClone() yourself
     *
     * @return Graph
     */
    public function createGraphCloneEdgeless()
    {
        $graph = new Graph();
//         $graph->setLayout($this->getLayout());
        // TODO: set additional graph attributes
        foreach ($this->getVertices() as $originalVertex) {
            $vertex = $graph->createVertexClone($originalVertex);
            // $graph->vertices[$vid] = $vertex;
        }

        return $graph;
    }

    /**
     * create new clone/copy of this graph - copy all attributes and vertices. but only copy all given edges
     *
     * @param  Edges|Edge[] $edges set or array of edges to be cloned
     * @return Graph
     * @uses Graph::createGraphCloneEdgeless()
     * @uses Graph::createEdgeClone() for each edge to be cloned
     */
    public function createGraphCloneEdges($edges)
    {
        $graph = $this->createGraphCloneEdgeless();
        foreach ($edges as $edge) {
            $graph->createEdgeClone($edge);
        }

        return $graph;
    }

    /**
     * create new clone/copy of this graph - copy all attributes, vertices and edges
     *
     * @return Graph
     * @uses Graph::createGraphCloneEdges() to clone graph with current edges
     */
    public function createGraphClone()
    {
        return $this->createGraphCloneEdges($this->edges);
    }

    /**
     * create a new clone/copy of this graph - copy all attributes and given vertices and its edges
     *
     * @param  Vertices $vertices set of vertices to keep
     * @return Graph
     * @uses Graph::createGraphClone() to create a complete clone
     * @uses Vertex::destroy() to remove unneeded vertices again
     */
    public function createGraphCloneVertices($vertices)
    {
        $verticesKeep = Vertices::factory($vertices);

        $graph = $this->createGraphClone();
        foreach ($graph->getVertices()->getMap() as $vid => $vertex) {
            if (!$verticesKeep->hasVertexId($vid)) {
                $vertex->destroy();
            }
        }

        return $graph;
    }

    /**
     * create new clone of the given edge between adjacent vertices
     *
     * @param  Edge $originalEdge original edge (not neccessarily from this graph)
     * @return Edge new edge in this graph
     * @uses Graph::createEdgeCloneInternal()
     */
    public function createEdgeClone(Edge $originalEdge)
    {
        return $this->createEdgeCloneInternal($originalEdge, 0, 1);
    }

    /**
     * create new clone of the given edge inverted (in opposite direction) between adjacent vertices
     *
     * @param  Edge $originalEdge original edge (not neccessarily from this graph)
     * @return Edge new edge in this graph
     * @uses Graph::createEdgeCloneInternal()
     */
    public function createEdgeCloneInverted(Edge $originalEdge)
    {
        return $this->createEdgeCloneInternal($originalEdge, 1, 0);
    }

    /**
     * create new clone of the given edge between adjacent vertices
     *
     * @param  Edge $originalEdge original edge from old graph
     * @param  int  $ia           index of start vertex
     * @param  int  $ib           index of end vertex
     * @return Edge new edge in this graph
     * @uses Edge::getVertices()
     * @uses Graph::getVertex()
     * @uses Vertex::createEdge() to create a new undirected edge if given edge was undrected
     * @uses Vertex::createEdgeTo() to create a new directed edge if given edge was directed
     * @uses Edge::getWeight()
     * @uses Edge::setWeight()
     * @uses Edge::getFlow()
     * @uses Edge::setFlow()
     * @uses Edge::getCapacity()
     * @uses Edge::setCapacity()
     */
    private function createEdgeCloneInternal(Edge $originalEdge, $ia, $ib)
    {
        $ends = $originalEdge->getVertices()->getIds();

        // get start vertex from old start vertex id
        $a = $this->getVertex($ends[$ia]);
        // get target vertex from old target vertex id
        $b = $this->getVertex($ends[$ib]);

        if ($originalEdge instanceof EdgeDirected) {
            $newEdge = $a->createEdgeTo($b);
        } else {
            // create new edge between new a and b
            $newEdge = $a->createEdge($b);
        }
        // TODO: copy edge attributes
        $newEdge->setLayout($originalEdge->getLayout());
        $newEdge->setWeight($originalEdge->getWeight());
        $newEdge->setFlow($originalEdge->getFlow());
        $newEdge->setCapacity($originalEdge->getCapacity());

        return $newEdge;
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
                $vertices[$id] = $this->verticesStorage[$id] = new Vertex($id, $this);
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
                $vertices[$id] = $this->verticesStorage[$id] = new Vertex($id, $this);
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
     * @return boolean
     */
    public function hasVertex($id)
    {
        return $this->vertices->hasVertexId($id);
    }

    /**
     * return first vertex found
     *
     * some algorithms do not need a particular vertex, but merely a (random)
     * starting point. this is a convenience function to just pick the first
     * vertex from the list of known vertices.
     *
     * @return Vertex             first vertex found in this graph
     * @throws UnderflowException if Graph has no vertices
     * @see Vertices::getVertexOrder() if you need to apply ordering first
     * @uses Vertices::getVertexFirst()
     */
    public function getVertexFirst()
    {
        return $this->vertices->getVertexFirst();
    }

    /**
     * adds a new Edge to the Graph (MUST NOT be called manually!)
     *
     * @param  Edge $edge instance of the new Edge
     * @return void
     * @private
     * @see Vertex::createEdge() instead!
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
     * @private
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
     * @private
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
     * Extracts edge from this graph
     *
     * @param  Edge               $edge
     * @return Edge
     * @throws UnderflowException if no edge was found
     * @throws OverflowException  if multiple edges match
     */
    public function getEdgeClone(Edge $edge)
    {
        // Extract endpoints from edge
        $vertices = $edge->getVertices()->getVector();

        return $this->getEdgeCloneInternal($edge, $vertices[0], $vertices[1]);
    }

    /**
     * Extracts inverted edge from this graph
     *
     * @param  Edge               $edge
     * @return Edge
     * @throws UnderflowException if no edge was found
     * @throws OverflowException  if multiple edges match
     */
    public function getEdgeCloneInverted(Edge $edge)
    {
        // Extract endpoints from edge
        $vertices = $edge->getVertices()->getVector();

        return $this->getEdgeCloneInternal($edge, $vertices[1], $vertices[0]);
    }

    private function getEdgeCloneInternal(Edge $edge, Vertex $startVertex, Vertex $targetVertex)
    {
        // Get original vertices from resultgraph
        $residualGraphEdgeStartVertex = $this->getVertex($startVertex->getId());
        $residualGraphEdgeTargetVertex = $this->getVertex($targetVertex->getId());

        // Now get the edge
        $residualEdgeArray = $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);
        $residualEdgeArray = Edges::factory($residualEdgeArray)->getVector();

        // Check for parallel edges
        if (!$residualEdgeArray) {
            throw new UnderflowException('No original edges for given cloned edge found');
        } elseif (count($residualEdgeArray) !== 1) {
            throw new OverflowException('More than one cloned edge? Parallel edges (multigraph) not supported');
        }

        return $residualEdgeArray[0];
    }

    /**
     * do NOT allow cloning of objects (MUST NOT be called!)
     *
     * @throws BadMethodCallException
     * @see Graph::createGraphClone() instead
     */
    private function __clone()
    {
        // @codeCoverageIgnoreStart
        throw new BadMethodCallException();
        // @codeCoverageIgnoreEnd
    }

    /**
     * set a new exporter to use when calling __toString()
     *
     * @param ExporterInterface $exporter
     * @return \Fhaculty\Graph\Graph $this (chainable)
     */
    public function setExporter(ExporterInterface $exporter)
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * get current exporter to use to export graph to its output format
     *
     * If no other exporter has been set previously, this will lazy-load
     * the (current default) Dot exporter.
     *
     * @return ExporterInterface
     */
    public function getExporter()
    {
        if ($this->exporter === null) {
            $this->exporter = new Dot();
        }
        return $this->exporter;
    }

    /**
     * export graph to its output format
     *
     * this is a magic method that will be called automatically when you
     * call `echo $graph;`.
     * @uses self::getExporter()
     * @uses ExporterInterface::getOutput()
     */
    public function __toString()
    {
        return $this->getExporter()->getOutput($this);
    }

    public function getLayout(){
        return array();
    }
}
