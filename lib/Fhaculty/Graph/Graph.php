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

class Graph extends Set
{
    /**
     * @var ExporterInterface|null
     * @see self::setExporter()
     */
    protected $exporter = null;

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
        if (isset($this->vertices[$id])) {
            if ($returnDuplicate) {
                return $this->vertices[$id];
            }
            throw new OverflowException('ID must be unique');
        }
        $vertex = new Vertex($id, $this);
        $this->vertices[$id] = $vertex;

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
        if (isset($this->vertices[$id])) {
            throw new RuntimeException('Id of cloned vertex already exists');
        }
        $newVertex = new Vertex($id, $this);
        // TODO: properly set attributes of vertex
        $newVertex->setLayout($originalVertex->getLayout());
        $newVertex->setBalance($originalVertex->getBalance());
        $newVertex->setGroup($originalVertex->getGroup());
        $this->vertices[$id] = $newVertex;

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
     * @param  Edge[] $edges array of edges to be cloned
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
     * @param  Vertex[] $vertices array of vertices to keep
     * @return Graph
     * @uses Graph::createGraphClone() to create a complete clone
     * @uses Vertex::destroy() to remove unneeded vertices again
     */
    public function createGraphCloneVertices($vertices)
    {
        $graph = $this->createGraphClone();
        foreach ($graph->getVertices() as $vid => $vertex) {
            if (!isset($vertices[$vid])) {
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
     * @uses Edge::getVerticesId()
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
        $ends = $originalEdge->getVerticesId();

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
     * create the given number of vertices
     *
     * @param  int   $n
     * @return Graph (chainable)
     * @uses Graph::getNextId()
     */
    public function createVertices($n)
    {
        for ($id = $this->getNextId(), $n += $id; $id < $n; ++$id) {
            $this->vertices[$id] = new Vertex($id, $this);
        }

        return $this;
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
        if (!$this->vertices) {
            return 0;
        }

        // auto ID
        return max(array_keys($this->vertices))+1;
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
        if( ! $this->hasVertex($id)){
            throw new OutOfBoundsException('Vertex '.$id.' does not exist');
        }

        return $this->vertices[$id];
    }

    /**
     * checks whether vertex is added to graph
     *
     * @param int|string $id identifier of Vertex
     * @return TRUE|FALSE
     */
    public function hasVertex($id){
      return isset($this->vertices[$id]);
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
     * @see Vertex::getFirst() if you need to apply ordering first
     */
    public function getVertexFirst()
    {
        foreach ($this->vertices as $vertex) {
            return $vertex;
        }

        throw new UnderflowException('Graph has no vertices');
    }

    /**
     * get degree for k-regular-graph (only if each vertex has the same degree)
     *
     * @return int
     * @throws UnderflowException       if graph is empty
     * @throws UnexpectedValueException if graph is not regular (i.e. vertex degrees are not equal)
     * @uses Vertex::getDegree()
     */
    public function getDegree()
    {
        // get initial degree of any start vertex to compare others to
        $degree = $this->getVertexFirst()->getDegree();

        foreach ($this->vertices as $vertex) {
            $i = $vertex->getDegree();

            if ($i !== $degree) {
                throw new UnexpectedValueException('Graph is not k-regular (vertex degrees differ)');
            }
        }

        return $degree;
    }

    /**
     * get minimum degree of vertices
     *
     * @return int
     * @throws Exception if graph is empty or directed
     * @uses Vertex::getFirst()
     * @uses Vertex::getDegree()
     */
    public function getDegreeMin()
    {
        return Vertex::getFirst($this->vertices, Vertex::ORDER_DEGREE)->getDegree();
    }

    /**
     * get maximum degree of vertices
     *
     * @return int
     * @throws Exception if graph is empty or directed
     * @uses Vertex::getFirst()
     * @uses Vertex::getDegree()
     */
    public function getDegreeMax()
    {
        return Vertex::getFirst($this->vertices, Vertex::ORDER_DEGREE, true)->getDegree();
    }

    /**
     * checks whether this graph is regular, i.e. each vertex has the same indegree/outdegree
     *
     * @return boolean
     * @uses Graph::getDegree()
     */
    public function isRegular()
    {
        // an empty graph is considered regular
        if (!$this->vertices) {
            return true;
        }
        try {
            $this->getDegree();

            return true;
        } catch (UnexpectedValueException $ignore) { }

        return false;
    }

    /**
     * check whether graph is connected (i.e. there's a connection between all vertices)
     *
     * @return boolean
     * @see Graph::getNumberOfComponents()
     * @uses AlgorithmConnectedComponents::isSingle()
     */
    public function isConnected()
    {
        $alg = new AlgorithmConnectedComponents($this);

        return $alg->isSingle();
    }

    /**
     * check whether this graph has an eulerian cycle
     *
     * @return boolean
     * @uses AlgorithmEulerian::hasCycle()
     * @link http://en.wikipedia.org/wiki/Eulerian_path
     */
    public function hasEulerianCycle()
    {
        $alg = new AlgorithmEulerian($this);

        return $alg->hasCycle();
    }

    /**
     * checks whether this graph is trivial (one vertex and no edges)
     *
     * @return boolean
     */
    public function isTrivial()
    {
        return (!$this->edges && count($this->vertices) === 1);
    }

    /**
     * checks whether this graph is symmetric (for every edge a->b there's also an edge b->a)
     *
     * @return boolean
     * @uses EdgeDirected::getVertexStart()
     * @uses EdgeDirected::getVertedEnd()
     * @uses Vertex::hasEdgeTo()
     */
    public function isSymmetric()
    {
        // check all edges
        foreach ($this->edges as $edge) {
            // only check directed edges (undirected ones are symmetric by definition)
            if ($edge instanceof EdgeDirected) {
                // check if end also has an edge to start
                if (!$edge->getVertexEnd()->hasEdgeTo($edge->getVertexStart())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * checks whether this graph has any parallel edges (aka multigraph)
     *
     * @return boolean
     * @uses Edge::hasEdgeParallel() for every edge
     */
    public function hasEdgeParallel()
    {
        foreach ($this->edges as $edge) {
            if ($edge->hasEdgeParallel()) {
                return true;
            }
        }

        return false;
    }

    /**
     * checks whether this graph is empty (no vertex - and thus no edges, aka null graph)
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return !$this->vertices;
    }

    /**
     * checks whether this graph has no edges
     *
     * @return boolean
     */
    public function isEdgeless()
    {
        return !$this->edges;
    }

    /**
     * checks whether this graph is complete (every vertex has an edge to any other vertex)
     *
     * @return boolean
     * @uses Vertex::hasEdgeTo()
     */
    public function isComplete()
    {
        // copy of array (separate iterator but same vertices)
        $c = $this->vertices;
        // from each vertex
        foreach ($this->vertices as $vertex) {
            // to each vertex
            foreach ($c as $other) {
                // missing edge => fail
                if ($other !== $vertex && !$vertex->hasEdgeTo($other)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * checks whether the indegree of every vertex equals its outdegree
     *
     * @return boolean
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     */
    public function isBalanced()
    {
        foreach ($this->vertices as $vertex) {
            if ($vertex->getDegreeIn() !== $vertex->getDegreeOut()) {
                return false;
            }
        }

        return true;
    }

    public function getBalance()
    {
        $balance = 0;
        // Sum for all vertices of value
        foreach ($this->getVertices() as $vertex) {
            $balance += $vertex->getBalance();
        }

        return $balance;
    }

    /**
     * check if the current flow is balanced (aka "balanced flow" or "b-flow")
     *
     * a flow is considered balanced if each edge's current flow does not exceed its
     * maximum capacity (which is always guaranteed due to the implementation
     * of Edge::setFlow()) and each vertices' flow (i.e. outflow-inflow) equals
     * its balance.
     *
     * checking whether the FLOW is balanced is not to be confused with checking
     * whether the GRAPH is balanced (see Graph::isBalanced() instead)
     *
     * @return boolean
     * @see Graph::isBalanced() if you merely want to check indegree=outdegree
     * @uses Vertex::getFlow()
     * @uses Vertex::getBalance()
     */
    public function isBalancedFlow()
    {
        // no need to check for each edge: flow <= capacity (setters already check that)
        // check for each vertex: outflow-inflow = balance
        foreach ($this->vertices as $vertex) {
            if ($vertex->getFlow() === $vertex->getBalance()) {
                return false;
            }
        }

        return true;
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
        $this->edges []= $edge;
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
        $id = array_search($edge, $this->edges, true);
        if ($id === false) {
            throw new InvalidArgumentException('Given edge does NOT exist');
        }
        unset($this->edges[$id]);
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
        $id = array_search($vertex, $this->vertices, true);
        if ($id === false) {
            throw new InvalidArgumentException('Given vertex does NOT exist');
        }
        unset($this->vertices[$id]);
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
        $vertices = $edge->getVertices();

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
        $vertices = $edge->getVertices();

        return $this->getEdgeCloneInternal($edge, $vertices[1], $vertices[0]);
    }

    private function getEdgeCloneInternal(Edge $edge, Vertex $startVertex, Vertex $targetVertex)
    {
        // Get original vertices from resultgraph
        $residualGraphEdgeStartVertex = $this->getVertex($startVertex->getId());
        $residualGraphEdgeTargetVertex = $this->getVertex($targetVertex->getId());

        // Now get the edge
        $residualEdgeArray = $residualGraphEdgeStartVertex->getEdgesTo($residualGraphEdgeTargetVertex);

        // Check for parallel edges
        if (!$residualEdgeArray) {
            throw new UnderflowException('No original edges for given cloned edge found');
        } elseif (count($residualEdgeArray) !== 1) {
            throw new OverflowException('More than one cloned edge? Parallel edges (multigraph) not supported');
        }

        return $residualEdgeArray[0];
    }

    /**
     * @return int number of components of this graph
     * @uses AlgorithmConnectedComponents::getNumberOfComponents()
     */
    public function getNumberOfComponents()
    {
        $alg = new AlgorithmConnectedComponents($this);

        return $alg->getNumberOfComponents();
    }

    /**
     * count total number of different groups assigned to vertices
     *
     * @return int
     * @uses AlgorithmGroups::getNumberOfGroups()
     */
    public function getNumberOfGroups()
    {
        $alg = new AlgorithmGroups($this);

        return $alg->getNumberOfGroups();
    }

    public function isBipartit()
    {
        $alg = new AlgorithmBipartit($this);

        return $alg->isBipartit();
    }

    /**
     * do NOT allow cloning of objects (MUST NOT be called!)
     *
     * @throws BadMethodCallException
     * @see Graph::createGraphClone() instead
     */
    private function __clone()
    {
        throw new BadMethodCallException();
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
