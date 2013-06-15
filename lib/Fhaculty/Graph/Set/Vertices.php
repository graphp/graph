<?php

namespace Fhaculty\Graph\Set;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Countable;
use IteratorAggregate;
use IteratorIterator;
use ArrayIterator;
use Fhaculty\Graph\Set\VerticesAggregate;
use Fhaculty\Graph\Set\VerticesMap;

class Vertices implements Countable, IteratorAggregate, VerticesAggregate
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

    protected $vertices = array();

    /**
     * create new Vertices instance
     *
     * @param array|Vertices|VerticesAggregate $vertices
     * @return Vertices
     */
    public static function factory($vertices)
    {
        if ($vertices instanceof VerticesAggregate) {
            return $vertices->getVertices();
        }
        return new self($vertices);
    }

    /**
     *
     * @param array $verticesArray
     */
    public static function factoryArrayReference(array &$verticesArray)
    {
        $vertices = new static();
        $vertices->vertices =& $verticesArray;
        return $vertices;
    }

    public function __construct(array $vertices = array())
    {
        $this->vertices = $vertices;
    }

    public function getVertexId($id)
    {
        try {
            return $this->getVertexMatch($this->getCallbackId($id));
        }
        catch (UnderflowException $e) {
            throw new OutOfBoundsException('Vertex ' . $id . ' does not exist', 0, $e);
        }
    }

    /**
     * checks whether given vertex ID exists in this set of vertices
     *
     * @param int|string $id identifier of Vertex
     * @return boolean
     */
    public function hasVertexId($id)
    {
        return $this->hasVertexMatch($this->getCallbackId($id));
    }

    /**
     * get array index for given Vertex
     *
     * not every set of Vertices represents a map, as such array index and
     * Vertex ID do not necessarily have to match.
     *
     * @param Vertex $vertex
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function getIndexVertex(Vertex $vertex)
    {
        $id = array_search($vertex, $this->vertices, true);
        if ($id === false) {
            throw new InvalidArgumentException('Given vertex does NOT exist');
        }
        return $id;
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
     * @see self::getVertexOrder() if you need to apply ordering first
     */
    public function getVertexFirst()
    {
        foreach ($this->vertices as $vertex) {
            return $vertex;
        }
        throw new UnderflowException('Does not contain any vertices');
    }

    public function getVertexMatchOrNull($callbackCheck)
    {
        foreach ($this->vertices as $vertex) {
            if ($callbackCheck($vertex)) {
                return $vertex;
            }
        }
        return null;
    }

    public function getVertexMatch($callbackCheck)
    {
        $ret = $this->getVertexMatchOrNull($callbackCheck);
        if ($ret === null) {
            throw new UnderflowException('No vertex found');
        }
        return $ret;
    }

    public function hasVertexMatch($callbackCheck)
    {
        return ($this->getVertexMatchOrNull($callbackCheck) !== null);
    }

    /**
     * get a new set of Vertices that match the given callback filter function
     *
     * @param callable $callbackCheck
     * @return Vertices a new Vertices instance
     */
    public function getVerticesMatch($callbackCheck)
    {
        return new self(array_filter($callbackCheck, $this->vertices));
    }

    /**
     * get iterator for vertices (optionally ordered by given criterium $by) from given array of vertices
     *
     * @param  int                      $orderBy       criterium to sort by. see Vertex::ORDER_ID, etc.
     * @param  boolean                  $desc     whether to return biggest first (true) instead of smallest first (default:false)
     * @return Iterator                 iterator object (supporting at the very least foreach)
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnexpectedValueException if trying to sort by reverse string IDs
     * @uses Vertex::getId()
     * @uses Vertex::getDegree()
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     * @uses Vertex::getGroup()
     */
    public function getVerticesOrder($orderBy = self::ORDER_FIFO, $desc = false)
    {
        if ($orderBy === self::ORDER_FIFO) {
            return new self($desc ? array_reverse($this->vertices, true) : $this->vertices);
        }
        if ($orderBy === self::ORDER_RANDOM) {
            $vertices = $this->vertices;
            shuffle($vertices);

            // create iterator for shuffled array (no need to check DESC flag)
            return new self($vertices);
        }
        $it = new SplPriorityQueue();
        foreach ($this->vertices as $vertex) {
            if ($orderBy === self::ORDER_ID) {
                $now = $vertex->getId();
                if ($desc && is_string($now)) {
                    throw new UnexpectedValueException('Unable to reverse sorting for string IDs');
                }
            } elseif ($orderBy === self::ORDER_DEGREE) {
                $now = $vertex->getDegree();
            } elseif ($orderBy === self::ORDER_INDEGREE) {
                $now = $vertex->getDegreeIn();
            } elseif ($orderBy === self::ORDER_OUTDEGREE) {
                $now = $vertex->getDegreeOut();
            } elseif ($orderBy === self::ORDER_GROUP) {
                $now = $vertex->getGroup();
            } else {
                throw new InvalidArgumentException('Invalid order flag "' . $orderBy . '"');
            }
            if ($desc && $now !== NULL) {
                $now = -$now;
            }
            $it->insert($vertex, $now);
        }

        return $it;
    }

    /**
     * get first vertex (optionally ordered by given criterium $by) from given array of vertices
     *
     * @param  int                      $orderBy  criterium to sort by. see Vertex::ORDER_ID, etc.
     * @param  boolean                  $desc     whether to return biggest (true) instead of smallest (default:false)
     * @return Vertex
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnderflowException       if no vertices exist
     * @uses Vertex::getId()
     * @uses Vertex::getDegree()
     * @uses Vertex::getDegreeIn()
     * @uses Vertex::getDegreeOut()
     * @uses Vertex::getGroup()
     */
    public function getVertexOrder($orderBy, $desc=false)
    {
        if (!$this->vertices) {
            throw new UnderflowException('No vertex found');
        }
        // random order
        if ($orderBy === self::ORDER_RANDOM) {
            // just return by random key (no need to check for DESC flag)
            return $this->vertices[array_rand($this->vertices)];
        }
        $ret = NULL;
        $best = NULL;
        foreach ($this->vertices as $vertex) {
            // do not sort - needs special handling
            if ($orderBy === self::ORDER_FIFO) {
                // always remember vertex from last iteration
                if ($desc) {
                    $ret = $vertex;
                    continue;
                    // just return first vertex right away
                } else {
                    return $vertex;
                }
            } elseif ($orderBy === self::ORDER_ID) {
                $now = $vertex->getId();
            } elseif ($orderBy === self::ORDER_DEGREE) {
                $now = $vertex->getDegree();
            } elseif ($orderBy === self::ORDER_INDEGREE) {
                $now = $vertex->getDegreeIn();
            } elseif ($orderBy === self::ORDER_OUTDEGREE) {
                $now = $vertex->getDegreeOut();
            } elseif ($orderBy === self::ORDER_GROUP) {
                $now = $vertex->getGroup();
            } else {
                throw new InvalidArgumentException('Invalid order flag "' . $orderBy . '"');
            }
            if ($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)) {
                $ret = $vertex;
                $best = $now;
            }
        }

        return $ret;
    }

    public function getVertices()
    {
        return $this;
    }

    /**
     * get a new set of Vertices where each Vertex is distinct/unique
     *
     * @return VerticesMap a new VerticesMap instance
     * @uses self::getMap()
     */
    public function getVerticesDistinct()
    {
        return new VerticesMap($this->getMap());
    }

    /**
     * get a mapping array of Vertex ID => Vertex instance and thus remove duplicate vertices
     *
     * @return Vertex[] Vertex ID => Vertex instance
     * @uses Vertex::getId()
     */
    public function getMap()
    {
        $vertices = array();
        foreach ($this->vertices as $vertex) {
            $vertices[$vertex->getId()] = $vertex;
        }
        return $vertices;
    }

    public function getIds()
    {
        $ids = array();
        foreach ($this->vertices as $vertex) {
            $ids []= $vertex->getId();
        }
        return $ids;
    }

    public function getVector()
    {
        return array_values($this->vertices);
    }

    public function count()
    {
        return count($this->vertices);
    }

    public function isEmpty()
    {
        return !$this->vertices;
    }

    public function getIterator()
    {
        return new IteratorIterator(new ArrayIterator($this->vertices));
    }

    private function getCallbackId($id)
    {
        return function (Vertex $vertex) use ($id) {
            return ($vertex->getId() == $id);
        };
    }
}
