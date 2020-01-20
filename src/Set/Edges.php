<?php

namespace Graphp\Graph\Set;

use Graphp\Graph\Edge;
use Graphp\Graph\Exception\OutOfBoundsException;
use Graphp\Graph\Exception\UnderflowException;

/**
 * A Set of Edges
 *
 * Contains any number of Edge (directed and/or undirected) instances.
 *
 * The Set is a readonly instance and it provides methods to get single Edge
 * instances or to get a new Set of Edges. This way it's safe to pass around
 * the original Set of Edges, because it will never be modified.
 */
class Edges implements \Countable, \IteratorAggregate, EdgesAggregate
{
    protected $edges = array();

    /**
     * create new Edges instance
     *
     * You can pass in just about anything that can be expressed as a Set of
     * Edges, such as:
     * - an array of Edge instances
     * - any Algorithm that implements the EdgesAggregate interface
     * - a Graph instance or
     * - an existing Set of Edges which will be returned as-is
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
     * instantiate new Set of Edges
     *
     * @param Edge[] $edges
     */
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
        $id = \array_search($edge, $this->edges, true);
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
        \reset($this->edges);

        return \current($this->edges);
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
        \end($this->edges);

        return \current($this->edges);
    }

    /**
     * return random Edge in this set of Edges
     *
     * @return Edge               random Edge in this set of Edges
     * @throws UnderflowException if set is empty
     * @see self::getEdgesShuffled()
     */
    public function getEdgeRandom()
    {
        if (!$this->edges) {
            throw new UnderflowException('Does not contain any edges');
        }

        return $this->edges[\array_rand($this->edges)];
    }

    /**
     * return Edge at given array index
     *
     * @param mixed $index
     * @throws OutOfBoundsException if the given index does not exist
     * @return Edge
     */
    public function getEdgeIndex($index)
    {
        if (!isset($this->edges[$index])) {
            throw new OutOfBoundsException('Invalid edge index');
        }
        return $this->edges[$index];
    }

    /**
     * return first Edge that matches the given callback filter function
     *
     * @param callable $callbackCheck
     * @return Edge
     * @throws UnderflowException if no Edge matches the given callback filter function
     * @uses self::getEdgeMatchOrNull()
     * @see self::getEdgesMatch() if you want to return *all* Edges that match
     */
    public function getEdgeMatch($callbackCheck)
    {
        $ret = $this->getEdgeMatchOrNull($callbackCheck);
        if ($ret === null) {
            throw new UnderflowException('No edge found');
        }
        return $ret;
    }

    /**
     * checks whethere there's an Edge that matches the given callback filter function
     *
     * @param callable $callbackCheck
     * @return bool
     * @see self::getEdgeMatch() to return the Edge instance that matches the given callback filter function
     * @uses self::getEdgeMatchOrNull()
     */
    public function hasEdgeMatch($callbackCheck)
    {
        return ($this->getEdgeMatchOrNull($callbackCheck) !== null);
    }

    /**
     * get a new set of Edges that match the given callback filter function
     *
     * This only keeps Edge elements if the $callbackCheck returns a bool
     * true and filters out everything else.
     *
     * Edge index positions will be left unchanged.
     *
     * @param callable $callbackCheck
     * @return Edges a new Edges instance
     * @see self::getEdgeMatch()
     */
    public function getEdgesMatch($callbackCheck)
    {
        return new static(\array_filter($this->edges, $callbackCheck));
    }

    /**
     * get new set of Edges ordered by given criterium $orderBy
     *
     * Edge index positions will be left unchanged.
     *
     * @param  string|callable(Edge):number $orderBy criterium to sort by. see edge attribute names or custom callable
     * @param  bool                         $desc    whether to return biggest first (true) instead of smallest first (default:false)
     * @return Edges                        a new Edges set ordered by the given $orderBy criterium
     */
    public function getEdgesOrder($orderBy, $desc = false)
    {
        $callback = $this->getCallback($orderBy);
        $array    = $this->edges;

        \uasort($array, function (Edge $va, Edge $vb) use ($callback, $desc) {
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
     * get new Set of Edges shuffled by random order
     *
     * Edge index positions will be left unchanged.
     *
     * @return Edges a new Edges set shuffled by random order
     * @see self::getEdgesOrder()
     * @see self::getEdgeRandom()
     */
    public function getEdgesShuffled()
    {
        // shuffle the edge positions
        $keys = \array_keys($this->edges);
        \shuffle($keys);

        // re-order according to shuffled edge positions
        $edges = array();
        foreach ($keys as $key) {
            $edges[$key] = $this->edges[$key];
        }

        return new static($edges);
    }

    /**
     * get first edge ordered by given criterium $orderBy
     *
     * @param  string|callable(Edge):number $orderBy criterium to sort by. see edge attribute names or custom callable
     * @param  bool                         $desc    whether to return biggest (true) instead of smallest (default:false)
     * @return Edge
     * @throws UnderflowException           if no edges exist
     */
    public function getEdgeOrder($orderBy, $desc=false)
    {
        if (!$this->edges) {
            throw new UnderflowException('No edge found');
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

    /**
     * return self reference to Set of Edges
     *
     * @return Edges
     * @see self::factory()
     */
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
            if (!\in_array($edge, $edges, true)) {
                $edges []= $edge;
            }
        }

        return new Edges($edges);
    }

    /**
     * get intersection of Edges with given other Edges
     *
     * The intersection contains all Edge instances that are present in BOTH
     * this set of Edges and the given set of other Edges.
     *
     * Edge index/keys will be preserved from original array.
     *
     * Duplicate Edge instances will be kept if the corresponding number of
     * Edge instances is also found in $otherEdges.
     *
     * @param Edges $otherEdges
     * @return Edges a new Edges set
     */
    public function getEdgesIntersection(Edges $otherEdges)
    {
        $otherArray = \iterator_to_array($otherEdges, false);

        $edges = array();
        foreach ($this->edges as $eid => $edge) {
            $i = \array_search($edge, $otherArray, true);

            if ($i !== false) {
                // remove from other array in order to check for duplicate matches
                unset($otherArray[$i]);

                $edges[$eid] = $edge;
            }
        }

        return new static($edges);
    }

    /**
     * return array of Edge instances
     *
     * @return Edge[]
     */
    public function getVector()
    {
        return \array_values($this->edges);
    }

    /**
     * count number of Edges
     *
     * @return int
     * @see self::isEmpty()
     */
    public function count()
    {
        return \count($this->edges);
    }

    /**
     * check whether this Set of Edges is empty
     *
     * A Set if empty if no single Edge instance is added. This is faster
     * than calling `count() === 0`.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return !$this->edges;
    }

    /**
     * get Iterator
     *
     * This method implements the IteratorAggregate interface and allows this
     * Set of Edges to be used in foreach loops.
     *
     * @return \IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator(new \ArrayIterator($this->edges));
    }

    /**
     * call given $callback on each Edge and sum their results
     *
     * @param string|callable(Edge):number $callback callback to sum by. See edge attribute names or custom callback
     * @return number
     * @uses self::getCallback()
     */
    public function getSumCallback($callback)
    {
        $callback = $this->getCallback($callback);

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
     * @param string|callable(Edge):number $callback
     * @return callable(Edge):number
     */
    private function getCallback($callback)
    {
        if (\is_callable($callback)) {
            if (\is_array($callback)) {
                $callback = function (Edge $edge) use ($callback) {
                    return \call_user_func($callback, $edge);
                };
            }
            return $callback;
        }

        return function (Edge $edge) use ($callback) {
            return $edge->getAttribute($callback);
        };

    }
}
