<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Algorithm\ShortestPath\BreadthFirst as AlgorithmSpBreadthFirst;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Edge\UndirectedId as EdgeUndirectedId;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\EdgesAggregate;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Exception\BadMethodCallException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Exception\InvalidArgumentException;

class Vertex extends Layoutable implements EdgesAggregate
{
    private $id;

    /**
     * @var Edge[]
     */
    private $edges = array();

    /**
     * @var Graph
     */
    private $graph;

    /**
     * vertex balance
     *
     * @var float|NULL
     * @see Vertex::setBalance()
     */
    private $balance;

    /**
     * group number
     *
     * @var int
     * @see Vertex::setGroup()
     */
    private $group = 0;

    /**
     * Creates a Vertex (MUST NOT BE CALLED MANUALLY!)
     *
     * @param string|int $id    identifier used to uniquely identify this vertex in the graph
     * @param Graph      $graph graph to be added to
     * @see Graph::createVertex() to create new vertices
     */
    public function __construct($id, Graph $graph)
    {
        $this->id = $id;
        $this->graph = $graph;
    }

    /**
     * get graph this vertex is attached to
     *
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    public function setBalance($balance)
    {
        if ($balance !== NULL && !is_float($balance) && !is_int($balance)) {
            throw new InvalidArgumentException('Invalid balance given - must be numeric');
        }
        $this->balance = $balance;

        return $this;
    }

    /**
     * set group number of this vertex
     *
     * @param  int                      $group
     * @return Vertex                   $this (chainable)
     * @throws InvalidArgumentException if group is not numeric
     */
    public function setGroup($group)
    {
        if (!is_int($group)) {
            throw new InvalidArgumentException('Invalid group number');
        }
        $this->group = $group;

        return $this;
    }

    /**
     * get group number
     *
     * @return int
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * returns id of this Vertex
     *
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * checks whether this start vertex has a path to the given target vertex
     *
     * @param  Vertex  $vertex
     * @return boolean
     * @uses AlgorithmSpBreadthFirst::hasVertex()
     */
    public function hasPathTo(Vertex $vertex)
    {
        $alg = new AlgorithmSpBreadthFirst($this);

        return $alg->hasVertex($vertex);
    }

    /**
     * checks whether the given vertex has a path TO THIS vertex
     *
     * @param  Vertex  $vertex
     * @return boolean
     * @uses Vertex::hasPathTo()
     */
    public function hasPathFrom(Vertex $vertex)
    {
        return $vertex->hasPathTo($this);
    }

    /**
     * get set of Vertices this vertex has a path to
     *
     * @return Vertices
     * @uses AlgorithmSpBreadthFirst::getVertices()
     */
    public function getVerticesPathTo()
    {
        $alg = new AlgorithmSpBreadthFirst($this);

        return $alg->getVertices();
    }

    /**
     * get set of Vertices that have a path to this vertex
     *
     * @return Vertices
     * @uses AlgorithmSpBreadthFirst::getVertices()
     */
    public function getVerticesPathFrom()
    {
        throw new BadMethodCallException('TODO');

        $alg = new AlgorithmSpBreadthFirst($this, true);

        return $alg->getVertices();
    }

    /**
     * create new directed edge from this start vertex to given target vertex
     *
     * @param  Vertex                   $vertex target vertex
     * @return EdgeDirected
     * @throws InvalidArgumentException
     * @uses Graph::addEdge()
     */
    public function createEdgeTo(Vertex $vertex)
    {
        if ($vertex->getGraph() !== $this->graph) {
            throw new InvalidArgumentException('Target vertex has to be within the same graph');
        }

        $edge = new EdgeDirected($this, $vertex);
        $this->edges []= $edge;
        $vertex->edges []= $edge;
        $this->graph->addEdge($edge);

        return $edge;
    }

    /**
     * add new undirected (bidirectional) edge between this vertex and given vertex
     *
     * @param  Vertex                   $vertex
     * @return EdgeUndirected
     * @throws InvalidArgumentException
     * @uses Graph::addEdge()
     */
    public function createEdge(Vertex $vertex)
    {
        if ($vertex->getGraph() !== $this->graph) {
            throw new InvalidArgumentException('Target vertex has to be within the same graph');
        }

        $edge = new EdgeUndirectedId($this, $vertex);
        $this->edges []= $edge;
        $vertex->edges []= $edge;
        $this->graph->addEdge($edge);

        return $edge;
    }

    /**
     * remove the given edge from list of connected edges (MUST NOT be called manually)
     *
     * @param  Edge                     $edge
     * @return void
     * @throws InvalidArgumentException if given edge does not exist
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
     * check whether this vertex has a direct edge to given $vertex
     *
     * @param  Vertex  $vertex
     * @return boolean
     * @uses Edge::hasVertexTarget()
     */
    public function hasEdgeTo(Vertex $vertex)
    {
        $that = $this;

        return $this->getEdges()->hasEdgeMatch(function (Edge $edge) use ($that, $vertex) {
            return $edge->isConnection($that, $vertex);
        });
    }

    /**
     * check whether the given vertex has a direct edge to THIS vertex
     *
     * @param  Vertex  $vertex
     * @return boolean
     * @uses Vertex::hasEdgeTo()
     */
    public function hasEdgeFrom(Vertex $vertex)
    {
        return $vertex->hasEdgeTo($this);
    }

    /**
     * get set of ALL Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdges()
    {
        return new Edges($this->edges);
    }

    /**
     * get set of all outgoing Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdgesOut()
    {
        $that = $this;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that) {
            return $edge->hasVertexStart($that);
        });
    }

    /**
     * get set of all ingoing Edges attached to this vertex
     *
     * @return Edges
     */
    public function getEdgesIn()
    {
        $that = $this;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that) {
            return $edge->hasVertexTarget($that);
        });
    }

    /**
     * get set of Edges FROM this vertex TO the given vertex
     *
     * @param  Vertex $vertex
     * @return Edges
     * @uses Edge::hasVertexTarget()
     */
    public function getEdgesTo(Vertex $vertex)
    {
        $that = $this;

        return $this->getEdges()->getEdgesMatch(function (Edge $edge) use ($that, $vertex) {
            return $edge->isConnection($that, $vertex);
        });
    }

    /**
     * get set of Edges FROM the given vertex TO this vertex
     *
     * @param  Vertex $vertex
     * @return Edges
     * @uses Vertex::getEdgesTo()
     */
    public function getEdgesFrom(Vertex $vertex)
    {
        return $vertex->getEdgesTo($this);
    }

    /**
     * get set of adjacent Vertices of this vertex (edge FROM or TO this vertex)
     *
     * @return Vertices
     * @uses Edge::hasVertexStart()
     * @uses Edge::getVerticesToFrom()
     * @uses Edge::getVerticesFromTo()
     */
    public function getVerticesEdge()
    {
        $ret = array();
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexStart($this)) {
                $vertex = $edge->getVertexToFrom($this);
            } else {
                $vertex = $edge->getVertexFromTo($this);
            }
            $ret[$vertex->getId()] = $vertex;
        }

        return new Vertices($ret);
    }

    /**
     * get set of all Vertices this vertex has an edge to
     *
     * @return Vertices
     * @uses Vertex::getEdgesOut()
     * @uses Edge::getVerticesToFrom()
     */
    public function getVerticesEdgeTo()
    {
        $ret = array();
        foreach ($this->getEdgesOut() as $edge) {
            $vertex = $edge->getVertexToFrom($this);
            $ret[$vertex->getId()] = $vertex;
        }

        return new Vertices($ret);
    }

    /**
     * get set of all Vertices that have an edge TO this vertex
     *
     * @return Vertices
     * @uses Vertex::getEdgesIn()
     * @uses Edge::getVerticesFromTo()
     */
    public function getVerticesEdgeFrom()
    {
        $ret = array();
        foreach ($this->getEdgesIn() as $edge) {
            $vertex = $edge->getVertexFromTo($this);
            $ret[$vertex->getId()] = $vertex;
        }

        return new Vertices($ret);
    }

    /**
     * get degree of this vertex (total number of edges)
     *
     * vertex degree counts the total number of edges attached to this vertex
     * regardless of whether they're directed or not. loop edges are counted
     * twice as both start and end form a 'line' to the same vertex.
     *
     * @return int
     * @see Vertex::getDegreeIn()
     * @see Vertex::getDegreeOut()
     */
    public function getDegree()
    {
        return count($this->edges);
    }

    /**
     * check whether this vertex is isolated (i.e. has no edges attached)
     *
     * @return boolean
     */
    public function isIsolated()
    {
        return !$this->edges;
    }

    /**
     * get indegree of this vertex (number of edges TO this vertex)
     *
     * @return int
     * @uses Edge::hasVertexTarget()
     * @see Vertex::getDegree()
     */
    public function getDegreeIn()
    {
        $n = 0;
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexTarget($this)) {
                ++$n;
            }
        }

        return $n;
    }

    /**
     * get outdegree of this vertex (number of edges FROM this vertex TO other vertices)
     *
     * @return int
     * @uses Edge::hasVertexStart()
     * @see Vertex::getDegree()
     */
    public function getDegreeOut()
    {
        $n = 0;
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexStart($this)) {
                ++$n;
            }
        }

        return $n;
    }

    /**
     * checks whether this vertex is a source, i.e. its indegree is zero
     *
     * @return boolean
     * @uses Edge::hasVertexTarget()
     * @see Vertex::getDegreeIn()
     */
    public function isSource()
    {
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexTarget($this)) {
                return false;
            }
        }

        // reach this point: no edge to this vertex
        return true;
    }

    /**
     * checks whether this vertex is a sink, i.e. its outdegree is zero
     *
     * @return boolean
     * @uses Edge::hasVertexStart()
     * @see Vertex::getDegreeOut()
     */
    public function isSink()
    {
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexStart($this)) {
                return false;
            }
        }

        // reach this point: no edge away from this vertex
        return true;
    }

    /**
     * checks whether this vertex has a loop (edge to itself)
     *
     * @return boolean
     * @uses Edge::isLoop()
     */
    public function hasLoop()
    {
        foreach ($this->edges as $edge) {
            if ($edge->isLoop()) {
                return true;
            }
        }

        return false;
    }

    /**
     * destroy vertex and all edges connected to it and remove reference from graph
     *
     * @uses Edge::destroy()
     * @uses Graph::removeVertex()
     */
    public function destroy()
    {
        foreach ($this->edges as $edge) {
            $edge->destroy();
        }
        $this->graph->removeVertex($this);
    }

    /**
     * do NOT allow cloning of objects
     *
     * @throws BadMethodCallException
     */
    private function __clone()
    {
        throw new BadMethodCallException();
    }
}
