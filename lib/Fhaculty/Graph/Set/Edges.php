<?php

namespace Fhaculty\Graph\Set;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Exception\UnderflowException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Countable;
use IteratorAggregate;
use IteratorIterator;
use ArrayIterator;
use Fhaculty\Graph\Set\EdgesAggregate;

class Edges implements Countable, IteratorAggregate, EdgesAggregate
{
    /**
     * do not change order - FIFO : first in, first out
     *
     * @var int
     */
    const ORDER_FIFO = 0;

    /**
     * order by edge weight
     *
     * @var int
     * @see Edge::getWeight()
     */
    const ORDER_WEIGHT = 1;

    /**
     * order by edge capacity
     *
     * @var int
     * @see Edge::getCapacity()
     */
    const ORDER_CAPACITY = 2;

    /**
     * order by remaining capacity on edge (maximum capacity - current flow)
     *
     * @var int
     * @see Edge::getCapacityRemaining()
     */
    const ORDER_CAPACITY_REMAINING = 3;

    /**
     * order by edge flow
     *
     * @var int
     * @see Edge::getFlow()
     */
    const ORDER_FLOW = 4;

    /**
     * random/shuffled order
     *
     * @var int
     */
    const ORDER_RANDOM = 5;

    protected $edges = array();

    /**
     * create new Edges instance
     *
     * @param array|Edges|EdgesAggregate $edges
     * @return Edges
     */
    public static function factory($edges)
    {
        if ($edges instanceof EdgesAggregate) {
            return $edges->getEdges();
        }
        return new self($edges);
    }

    /**
     *
     * @param array $edgesArray
     */
    public static function factoryArrayReference(array &$edgesArray)
    {
        $edges = new static();
        $edges->edges =& $edgesArray;
        return $edges;
    }

    public function __construct(array $edges = array())
    {
        $this->edges = $edges;
    }

    /**
     * get array index for given Edge
     *
     * @param Edge $edge
     * @throws OutOfBoundsException
     * @return mixed
     */
    public function getIndexEdge(Edge $edge)
    {
        $id = array_search($edge, $this->edges, true);
        if ($id === false) {
            throw new OutOfBoundsException('Given edge does NOT exist');
        }
        return $id;
    }

    /**
     * return first Edge in this set of Edges
     *
     * some algorithms do not need a particular edge, but merely a (random)
     * starting point. this is a convenience function to just pick the first
     * edge from the list of known edges.
     *
     * @return Edge               first Edge in this set of Edges
     * @throws UnderflowException if set is empty
     * @see self::getEdgeOrder()  if you need to apply ordering first
     */
    public function getEdgeFirst()
    {
        if (!$this->edges) {
            throw new UnderflowException('Does not contain any edges');
        }
        reset($this->edges);

        return current($this->edges);
    }

    /**
     * return last Edge in this set of Edges
     *
     * @return Edge               last Edge in this set of Edges
     * @throws UnderflowException if set is empty
     */
    public function getEdgeLast()
    {
        if (!$this->edges) {
            throw new UnderflowException('Does not contain any edges');
        }
        end($this->edges);

        return current($this->edges);
    }

    public function getEdgeMatch($callbackCheck)
    {
        $ret = $this->getEdgeMatchOrNull($callbackCheck);
        if ($ret === null) {
            throw new UnderflowException('No edge found');
        }
        return $ret;
    }

    public function hasEdgeMatch($callbackCheck)
    {
        return ($this->getEdgeMatchOrNull($callbackCheck) !== null);
    }

    /**
     * get a new set of Edges that match the given callback filter function
     *
     * This only keeps Edge elements if the $callbackCheck returns a boolean
     * true and filters out everything else.
     *
     * Edge index positions will be left unchanged, so if you call this method
     * on a EdgesMap, it will also return a EdgesMap.
     *
     * @param callable $callbackCheck
     * @return Edges a new Edges instance
     */
    public function getEdgesMatch($callbackCheck)
    {
        return new static(array_filter($this->edges, $callbackCheck));
    }

    /**
     * get iterator for edges (optionally ordered by given criterium $by) from given array of edges
     *
     * Edge index positions will be left unchanged, so if you call this method
     * on a EdgesMap, it will also return a EdgesMap.
     *
     * @param  int                      $orderBy  criterium to sort by. see self::ORDER_WEIGHT, etc.
     * @param  boolean                  $desc     whether to return biggest first (true) instead of smallest first (default:false)
     * @return Edges                 a new Edges set ordered by the given $orderBy criterium
     * @throws InvalidArgumentException if criterium is unknown
     * @uses Edge::getId()
     * @uses Edge::getDegree()
     * @uses Edge::getDegreeIn()
     * @uses Edge::getDegreeOut()
     * @uses Edge::getGroup()
     */
    public function getEdgesOrder($orderBy = self::ORDER_FIFO, $desc = false)
    {
        if ($orderBy === self::ORDER_RANDOM) {
            // shuffle the edge positions
            $keys = array_keys($this->edges);
            shuffle($keys);

            // re-order according to shuffled edge positions
            $edges = array();
            foreach ($keys as $key) {
                $edges[$key] = $this->edges[$key];
            }

            // create iterator for shuffled array (no need to check DESC flag)
            return new static($edges);
        }

        $callback = $this->getCallback($orderBy);
        $array    = $this->edges;

        uasort($array, function (Edge $va, Edge $vb) use ($callback, $desc) {
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
     * get first edge (optionally ordered by given criterium $by) from given array of edges
     *
     * @param  int                      $orderBy  criterium to sort by. see self::ORDER_WEIGHT, etc.
     * @param  boolean                  $desc     whether to return biggest (true) instead of smallest (default:false)
     * @return Edge
     * @throws InvalidArgumentException if criterium is unknown
     * @throws UnderflowException       if no edges exist
     * @uses Edge::getId()
     * @uses Edge::getDegree()
     * @uses Edge::getDegreeIn()
     * @uses Edge::getDegreeOut()
     * @uses Edge::getGroup()
     */
    public function getEdgeOrder($orderBy, $desc=false)
    {
        if (!$this->edges) {
            throw new UnderflowException('No edge found');
        }
        // random order
        if ($orderBy === self::ORDER_RANDOM) {
            // just return by random key (no need to check for DESC flag)
            return $this->edges[array_rand($this->edges)];
        }

        $callback = $this->getCallback($orderBy);

        $ret = NULL;
        $best = NULL;
        foreach ($this->edges as $edge) {
            $now = $callback($edge);

            if ($ret === NULL || ($desc && $now > $best) || (!$desc && $now < $best)) {
                $ret = $edge;
                $best = $now;
            }
        }

        return $ret;
    }

    public function getEdges()
    {
        return $this;
    }

    /**
     * get a new set of Edges where each Edge is distinct/unique
     *
     * @return Edges a new Edges instance
     */
    public function getEdgesDistinct()
    {
        $edges = array();
        foreach ($this->edges as $edge) {
            // filter duplicate edges
            if (!in_array($edge, $edges, true)) {
                $edges []= $edge;
            }
        }

        return new Edges($edges);
    }

    public function getVector()
    {
        return array_values($this->edges);
    }

    public function count()
    {
        return count($this->edges);
    }

    public function isEmpty()
    {
        return !$this->edges;
    }

    public function getIterator()
    {
        return new IteratorIterator(new ArrayIterator($this->edges));
    }

    /**
     * call given $callback on each Edge and sum their results
     *
     * @param callable $callback
     * @return number
     * @throws InvalidArgumentException for invalid callbacks
     * @uses self::getCallback()
     */
    public function getSumCallback($callback)
    {
        $callback = $this->getCallback($callback);

        // return array_sum(array_map($callback, $this->edges));

        $sum = 0;
        foreach ($this->edges as $edge) {
            $sum += $callback($edge);
        }
        return $sum;
    }

    private function getEdgeMatchOrNull($callbackCheck)
    {
        $callbackCheck = $this->getCallback($callbackCheck);

        foreach ($this->edges as $edge) {
            if ($callbackCheck($edge)) {
                return $edge;
            }
        }
        return null;
    }

    /**
     * get callback/Closure to be called on Edge instances for given callback identifier
     *
     * @param callable|int $callback
     * @throws InvalidArgumentException
     * @return Closure
     */
    private function getCallback($callback)
    {
        if (is_callable($callback)) {
            if (is_array($callback)) {
                $callback = function (Edge $edge) use ($callback) {
                    return call_user_func($callback, $edge);
                };
            }
            return $callback;
        }

        static $methods = array(
            self::ORDER_WEIGHT => 'getWeight',
            self::ORDER_CAPACITY => 'getCapacity',
            self::ORDER_CAPACITY_REMAINING => 'getCapacityRemaining',
            self::ORDER_FLOW => 'getFlow'
        );

        if (!is_int($callback) || !isset($methods[$callback])) {
            throw new InvalidArgumentException('Invalid callback given');
        }

        $method = $methods[$callback];

        return function (Edge $edge) use ($method) {
            return $edge->$method();
        };
    }
}
