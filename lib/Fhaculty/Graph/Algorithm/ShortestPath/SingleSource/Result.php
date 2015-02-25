<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath\SingleSource;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\VerticesAggregate;
use Fhaculty\Graph\Set\EdgesAggregate;

interface Result extends EdgesAggregate, VerticesAggregate
{
    /**
     * get walk (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @return Walk
     * @throws OutOfBoundsException if there's no path to the given end vertex
     */
    public function getWalkTo(Vertex $endVertex);

    /**
     * get array of edges (path) from start vertex to given end vertex
     *
     * @param  Vertex    $endVertex
     * @throws OutOfBoundsException if there's no path to the given end vertex
     * @return Edges
     */
    public function getEdgesTo(Vertex $endVertex);

    /**
     * get set of all Vertices the given start vertex has a path to
     *
     * @return Vertices
     */
    // public function getVertices();

    /**
     * checks whether there's a path from this start vertex to given end vertex
     *
     * @param  Vertex  $endVertex
     * @return boolean
     */
    public function hasWalkTo(Vertex $vertex);

    /**
     * get map of vertex IDs to distance
     *
     * @return float[]
     */
    public function getDistanceMap();

    /**
     * get distance (sum of weights) between start vertex and given end vertex
     *
     * @param  Vertex    $endVertex
     * @return float
     * @throws OutOfBoundsException if there's no path to the given end vertex
     */
    public function getDistanceTo(Vertex $endVertex);

    /**
     * create new resulting graph with only edges on shortest path
     *
     * The resulting Graph will always represent a tree with the start vertex
     * being the root vertex.
     *
     * For example considering the following input Graph with equal weights on
     * each edge:
     *
     *     A----->F
     *    / \     ^
     *   /   \   /
     *  /     \ /
     *  |      E
     *  |       \
     *  |        \
     *  B--->C<---D
     *
     * The resulting shortest path tree Graph will look like this:
     *
     *     A----->F
     *    / \
     *   /   \
     *  /     \
     *  |      E
     *  |       \
     *  |        \
     *  B--->C    D
     *
     * Or by just arranging the Vertices slightly different:
     *
     *          A
     *         /|\
     *        / | \
     *       B  E  \->F
     *      /   |
     *  C<-/    D
     *
     * @return Graph
     */
    public function createGraph();

    /**
     * get all edges on shortest path for this vertex
     *
     * @return Edges
     */
    // public function getEdges();
}
