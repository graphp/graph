<?php

namespace Fhaculty\Graph\Set;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Countable;
use IteratorAggregate;
use IteratorIterator;
use ArrayIterator;
use Fhaculty\Graph\Set\VerticesAggregate;
use Fhaculty\Graph\Set\VerticesMap;
use Fhaculty\Graph\Algorithm\Degree;

class Vertices implements Countable, IteratorAggregate, VerticesAggregate
{
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
     * @see Degree::getDegreeVertex()
     */
    const ORDER_DEGREE = 2;

    /**
     * order by indegree of vertex
     *
     * @var int
     * @see Degree::getDegreeInVertex()
     */
    const ORDER_INDEGREE = 3;

    /**
     * order by outdegree of vertex
     *
     * @var int
     * @see Degree::getDegreeOutVertex()
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
     * @throws OutOfBoundsException
     * @return mixed
     */
    public function getIndexVertex(Vertex $vertex)
    {
        $id = array_search($vertex, $this->vertices, true);
        if ($id === false) {
            throw new OutOfBoundsException('Given vertex does NOT exist');
        }
        return $id;
    }

    /**
     * return first Vertex in this set of Vertices
     *
     * some algorithms do not need a particular vertex, but merely a (random)
     * starting point. this is a convenience function to just pick the first
     * vertex from the list of known vertices.
     *
     * @return Vertex             first Vertex in this set of Vertices
     * @throws UnderflowException if set is empty
     * @see self::getVertexOrder() if you need to apply ordering first
     */
    public function getVertexFirst()
    {
        if (!$this->vertices) {
            throw new UnderflowException('Does not contain any vertices');
        }
        reset($this->vertices);

        return current($this->vertices);
    }

    /**
     * return last Vertex in this set of Vertices
     *
     * @return Vertex             last Vertex in this set of Vertices
     * @throws UnderflowException if set is empty
     */
    public function getVertexLast()
    {
        if (!$this->vertices) {
            throw new UnderflowException('Does not contain any vertices');
        }
        end($this->vertices);

        return current($this->vertices);
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
     * This only keeps Vertex elements if the $callbackCheck returns a boolean
     * true and filters out everything else.
     *
     * Vertex index positions will be left unchanged, so if you call this method
     * on a VerticesMap, it will also return a VerticesMap.
     *
     * @param callable $callbackCheck
     * @return Vertices a new Vertices instance
     */
    public function getVerticesMatch($callbackCheck)
    {
        return new static(array_filter($this->vertices, $callbackCheck));
    }

    /**
     * get iterator for vertices (optionally ordered by given criterium $by) from given array of vertices
     *
     * Vertex index positions will be left unchanged, so if you call this method
     * on a VerticesMap, it will also return a VerticesMap.
     *
     * @param  int                      $orderBy  criterium to sort by. see Vertex::ORDER_ID, etc.
     * @param  boolean                  $desc     whether to return biggest first (true) instead of smallest first (default:false)
     * @return Vertices                 a new Vertices set ordered by the given $orderBy criterium
     * @throws InvalidArgumentException if criterium is unknown
     * @uses Vertex::getId()
     * @uses Vertex::getGroup()
     */
    public function getVerticesOrder($orderBy = self::ORDER_FIFO, $desc = false)
    {
        if ($orderBy === self::ORDER_RANDOM) {
            // shuffle the vertex positions
            $keys = array_keys($this->vertices);
            shuffle($keys);

            // re-order according to shuffled vertex positions
            $vertices = array();
            foreach ($keys as $key) {
                $vertices[$key] = $this->vertices[$key];
            }

            // create iterator for shuffled array (no need to check DESC flag)
            return new static($vertices);
        }

        $callback = $this->getCallback($orderBy);
        $array    = $this->vertices;

        uasort($array, function (Vertex $va, Vertex $vb) use ($callback, $desc) {
            $ra = $callback($desc ? $vb : $va);
            $rb = $callback($desc ? $va : $vb);

            if ($ra < $rb) {
                return -1;
            } elseif ($ra > $rb) {
                return 1;
            } else {
                return 0;
            }
        });

        return new static($array);
    }

    /**
     * get intersection of Vertices with given other Vertices
     *
     * The intersection contains all Vertex instances that are present in BOTH
     * this set of Vertices and the given set of other Vertices.
     *
     * Vertex index/keys will be preserved from original array.
     *
     * Duplicate Vertex instances will be kept if the corresponding number of
     * Vertex instances is also found in $otherVertices.
     *
     * @param Vertices|Vertex[] $otherVertices
     * @return Vertices a new Vertices set
     */
    public function getVerticesIntersection($otherVertices)
    {
        $otherArray = self::factory($otherVertices)->getVector();

        $vertices = array();
        foreach ($this->vertices as $vid => $vertex) {
            $i = array_search($vertex, $otherArray, true);

            if ($i !== false) {
                // remove from other array in order to check for duplicate matches
                unset($otherArray[$i]);

                $vertices[$vid] = $vertex;
            }
        }

        return new static($vertices);
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

        $callback = $this->getCallback($orderBy);

        $ret = NULL;
        $best = NULL;
        foreach ($this->vertices as $vertex) {
            $now = $callback($vertex);

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

    /**
     * check whether this set contains any duplicate vertex instances
     *
     * @return boolean
     * @uses self::getMap()
     */
    public function hasDuplicates()
    {
        return (count($this->vertices) !== count($this->getMap()));
    }

    public function getIterator()
    {
        return new IteratorIterator(new ArrayIterator($this->vertices));
    }

    /**
     * call given $callback on each Vertex and sum their results
     *
     * @param callable $callback
     * @return number
     * @throws InvalidArgumentException for invalid callbacks
     * @uses self::getCallback()
     */
    public function getSumCallback($callback)
    {
        $callback = $this->getCallback($callback);

        // return array_sum(array_map($callback, $this->vertices));

        $sum = 0;
        foreach ($this->vertices as $vertex) {
            $sum += $callback($vertex);
        }
        return $sum;
    }

    private function getCallbackId($id)
    {
        return function (Vertex $vertex) use ($id) {
            return ($vertex->getId() == $id);
        };
    }

    private function getVertexMatchOrNull($callbackCheck)
    {
        $callbackCheck = $this->getCallback($callbackCheck);

        foreach ($this->vertices as $vertex) {
            if ($callbackCheck($vertex)) {
                return $vertex;
            }
        }
        return null;
    }

    /**
     * get callback/Closure to be called on Vertex instances for given callback identifier
     *
     * @param callable|int $callback
     * @throws InvalidArgumentException
     * @return Closure
     */
    private function getCallback($callback)
    {
        if (is_callable($callback)) {
            if (is_array($callback)) {
                $callback = function (Vertex $vertex) use ($callback) {
                    return call_user_func($callback, $vertex);
                };
            }
            return $callback;
        }

        static $methods = array(
            self::ORDER_ID => 'getId',
            self::ORDER_DEGREE => 'getDegreeVertex',
            self::ORDER_INDEGREE => 'getDegreeInVertex',
            self::ORDER_OUTDEGREE => 'getDegreeOutVertex',
            self::ORDER_GROUP => 'getGroup'
        );

        if (!is_int($callback) || !isset($methods[$callback])) {
            throw new InvalidArgumentException('Invalid callback given');
        }

        $method = $methods[$callback];

        if (in_array($callback, array(self::ORDER_DEGREE, self::ORDER_INDEGREE, self::ORDER_OUTDEGREE))) {
            $degree = new Degree($this->getGraph());
            return function (Vertex $vertex) use ($method, $degree) {
                return $degree->$method($vertex);
            };
        }

        return function (Vertex $vertex) use ($method) {
            return $vertex->$method();
        };
    }

    private function getGraph()
    {
        return $this->getVertexFirst()->getGraph();
    }
}
