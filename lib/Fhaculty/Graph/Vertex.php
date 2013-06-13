<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exception\BadMethodCallException;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Exception\InvalidArgumentException;

use \ArrayIterator;
use \SplPriorityQueue;
use Fhaculty\Graph\Algorithm\ShortestPath\BreadthFirst as AlgorithmSpBreadthFirst;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Edge\UndirectedId as EdgeUndirectedId;

class Vertex extends Layoutable
{
    /**
     * do not change order - FIFO : first in, first out
     *
     * @var int
     */
    const ORDER_FIFO = 0;

    /**
     * order by vertex ID
     *
     * @var int
     * @see Vertex::getId()
     */
    const ORDER_ID = 1;

    /**
     * order by vertex degree
     *
     * @var int
     * @see Vertex::getDegree()
     */
    const ORDER_DEGREE = 2;

    /**
     * order by indegree of vertex
     *
     * @var int
     * @see Vertex::getDegreeIn()
     */
    const ORDER_INDEGREE = 3;

    /**
     * order by outdegree of vertex
     *
     * @var int
     * @see Vertex::getDegreeOut()
     */
    const ORDER_OUTDEGREE = 4;

    /**
     * random/shuffled order
     *
     * @var int
     */
    const ORDER_RANDOM = 5;

    /**
     * order by vertex group
     *
     * @var int
     * @see Vertex::getGroup()
     */
    const ORDER_GROUP = 6;

    /**
     * get first vertex (optionally ordered by given criterium $by) from given array of vertices
     *
     * @param  Vertex[]|Graph           $vertices array of vertices to scan for 'first' vertex
     * @param  int                      $by       criterium to sort by. see Vertex::ORDER_ID, etc.
     * @param  boolean                  $desc     whether to return biggest (true) instead of smallest (default:false)
     * @return Vertex
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnderflowException       if no vertices exist
     * @uses Graph::getVertices() if graph is given instead of vertices
     * @uses Vertex::getId()
     * @uses Vertex::getDegree()
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     * @uses Vertex::getGroup()
     */
    public static function getFirst($vertices, $by = self::ORDER_FIFO, $desc = false)
    {
        if ($vertices instanceof Graph) {
            $vertices = $vertices->getVertices();
        }
        // random order and there are actually some vertices to shuffle
        if ($by === self::ORDER_RANDOM && $vertices) {

            // just return by random key (no need to check for DESC flag)
            return $vertices[array_rand($vertices)];
        }
        $ret = NULL;
        $best = NULL;
        foreach ($vertices as $vertex) {
            // do not sort - needs special handling
            if ($by === self::ORDER_FIFO) {
                // always remember vertex from last iteration
                if ($desc) {
                    $ret = $vertex;
                    continue;
                // just return first vertex right away
                } else {
                    return $vertex;
                }
            } elseif ($by === self::ORDER_ID) {
                $now = $vertex->getId();
            } elseif ($by === self::ORDER_DEGREE) {
                $now = $vertex->getDegree();
            } elseif ($by === self::ORDER_INDEGREE) {
                $now = $vertex->getDegreeIn();
            } elseif ($by === self::ORDER_OUTDEGREE) {
                $now = $vertex->getDegreeOut();
            } elseif ($by === self::ORDER_GROUP) {
                $now = $vertex->getGroup();
            } else {
                throw new InvalidArgumentException('Invalid order flag "' . $by . '"');
            }
            if ($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)) {
                $ret = $vertex;
                $best = $now;
            }
        }
        if ($ret === NULL) {
            throw new UnderflowException('No vertex found');
        }

        return $ret;
    }

    /**
     * get iterator for vertices (optionally ordered by given criterium $by) from given array of vertices
     *
     * @param  Vertex[]|Graph           $vertices array of vertices to scan for 'first' vertex
     * @param  int                      $by       criterium to sort by. see Vertex::ORDER_ID, etc.
     * @param  boolean                  $desc     whether to return biggest first (true) instead of smallest first (default:false)
     * @return Iterator                 iterator object (supporting at the very least foreach)
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnexpectedValueException if trying to sort by reverse string IDs
     * @uses Graph::getVertices() if graph is given instead of vertices
     * @uses Vertex::getId()
     * @uses Vertex::getDegree()
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     * @uses Vertex::getGroup()
     */
    public static function getAll($vertices, $by = self::ORDER_FIFO, $desc = false)
    {
        if ($vertices instanceof Graph) {
            $vertices = $vertices->getVertices();
        }
        if ($by === self::ORDER_FIFO) {
            return new ArrayIterator($desc ? array_reverse($vertices) : $vertices);
        }
        if ($by === self::ORDER_RANDOM) {
            shuffle($vertices);

            // create iterator for shuffled array (no need to check DESC flag)
            return new ArrayIterator($vertices);
        }
        $it = new SplPriorityQueue();
        foreach ($vertices as $vertex) {
            if ($by === self::ORDER_ID) {
                $now = $vertex->getId();
                if ($desc && is_string($now)) {
                    throw new UnexpectedValueException('Unable to reverse sorting for string IDs');
                }
            } elseif ($by === self::ORDER_DEGREE) {
                $now = $vertex->getDegree();
            } elseif ($by === self::ORDER_INDEGREE) {
                $now = $vertex->getDegreeIn();
            } elseif ($by === self::ORDER_OUTDEGREE) {
                $now = $vertex->getDegreeOut();
            } elseif ($by === self::ORDER_GROUP) {
                $now = $vertex->getGroup();
            } else {
                throw new InvalidArgumentException('Invalid order flag "' . $by . '"');
            }
            if ($desc && $now !== NULL) {
                $now = -$now;
            }
            $it->insert($vertex, $now);
        }

        return $it;
    }

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
     * get array of vertices this vertex has a path to
     *
     * @return Vertex[]
     * @uses AlgorithmSpBreadthFirst::getVertices()
     */
    public function getVerticesPathTo()
    {
        $alg = new AlgorithmSpBreadthFirst($this);

        return $alg->getVertices();
    }

    /**
     * get array of vertices that have a path to this vertex
     *
     * @return Vertex[]
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
        foreach ($this->edges as $edge) {
            if ($edge->isConnection($this, $vertex)) {
                return true;
            }
        }

        return false;
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
     * get ALL edges attached to this vertex
     *
     * @return Edge[]
     */
    public function getEdges()
    {
        return $this->edges;
    }

    /**
     * get ALL outgoing edges attached to this vertex
     *
     * @return Edge[]
     */
    public function getEdgesOut()
    {
        $outgoingEdges = array();
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexStart($this)) {
                $outgoingEdges[] = $edge;
            }
        }

        return $outgoingEdges;
    }

    /**
     * get ALL ingoing edges attached to this vertex
     *
     * @return Edge[]
     */
    public function getEdgesIn()
    {
        $ingoingEdges = array() ;
        foreach ($this->edges as $edge) {
            if ($edge->hasVertexTarget($this)) {
                $ingoingEdges[] = $edge;
            }
        }

        return $ingoingEdges;
    }

    /**
     * get edges FROM this vertex TO the given vertex
     *
     * @param  Vertex $vertex
     * @return Edge[]
     * @uses Edge::hasVertexTarget()
     */
    public function getEdgesTo(Vertex $vertex)
    {
        $ret = array();
        foreach ($this->edges as $edge) {
            if ($edge->isConnection($this, $vertex)) {
                $ret[] = $edge;
            }
        }

        return $ret;
    }

    /**
     * get edges FROM the given vertex TO this vertex
     *
     * @param  Vertex $vertex
     * @return Edge[]
     * @uses Vertex::getEdgesTo()
     */
    public function getEdgesFrom(Vertex $vertex)
    {
        return $vertex->getEdgesTo($this);
    }

    /**
     * get all adjacent vertices of this vertex (edge FROM or TO this vertex)
     *
     * @return Vertex[]
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

        return $ret;
    }

    /**
     * get all vertices this vertex has an edge to
     *
     * @return Vertex[]
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

        return $ret;
    }

    /**
     * get all vertices that have an edge TO this vertex
     *
     * @return Vertex[]
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

        return $ret;
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
     * check whether this is a leaf node (i.e. has only one edge)
     *
     * @return boolean
     * @throws Exception if this is directed graph
     * @uses Vertex::getDegree()
     * @todo check logic! should be indegree=1 and outdegree=0 for directed and degree=indegree=outdegree=1 for undirected?
     */
    public function isLeaf()
    {
        return ($this->getDegree() === 1);
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
