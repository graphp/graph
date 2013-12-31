<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Set\Vertices;

/**
 * Abstract base class for shortest path algorithms
 *
 * This abstract base class provides the base interface for working with
 * single-source shortest paths (SSSP).
 *
 * The shortest path problem is the problem of finding a path between two
 * vertices such that the sum of the weights of its constituent edges is
 * minimized. The weight of the shortest path is referred to as distance.
 *
 *    A--[10]-------------B---E<--F
 *     \                 /
 *      \--[4]--C--[2]--D
 *
 * In the above pictured graph, the distance (weight of the shortest path)
 * between A and C is 4, and the shortest path between A and B is "A->C->D->B"
 * with a distance (total weight) of 6.
 *
 * In graph theory, it is usually assumed that a path to an unreachable vertex
 * has infinite distance. In the above pictured graph, there's no way path
 * from A to F, i.e. vertex F is unreachable from vertex A because of the
 * directed edge "E <- F" pointing in the opposite direction. This library
 * considers this an Exception instead. So if you're asking for the distance
 * between A and F, you'll receive an OutOfBoundsException instead.
 *
 * In graph theory, it is usually assumed that each vertex has a (pseudo-)path
 * to itself with a distance of 0. In order to produce reliable, consistent
 * results, this library considers this (pseudo-)path to be non-existant, i.e.
 * there's NO "magic" path between F and F. So if you're asking for the distance
 * between F and F, you'll receive an OutOfBoundsException instead. This allows
 * us to check whether there's a real path between F and F (cycle via other
 * vertices) as well as working with loop edges.
 *
 * However, take note that there's a (hidden) path between E and E because the
 * edge "B - E" is undirected and one can traverse it as "E - B - E". Also,
 * similarily there's obviously a cycle path between A and A. However, the fact
 * that "A - C - A" is the shorter than "A - B - D - C" might be a bit concealed.
 *
 * @link http://en.wikipedia.org/wiki/Shortest_path_problem
 * @link http://en.wikipedia.org/wiki/Tree_%28data_structure%29
 * @see ShortestPath\Dijkstra
 * @see ShortestPath\MooreBellmanFord which also supports negative Edge weights
 * @see ShortestPath\BreadthFirst with does not consider Edge weights, but only the number of hops
 */
abstract class Base
{
    /**
     * @return Result
     */
    abstract public function createResult(Vertex $startVertex);
}
