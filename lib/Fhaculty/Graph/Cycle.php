<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Exception\UnderflowException;

class Cycle extends Walk
{
    /**
     * create new cycle instance from given predecessor map
     *
     * @param  Vertex[]           $predecessors map of vid => predecessor vertex instance
     * @param  Vertex             $vertex       start vertex to search predecessors from
     * @param  int                $by
     * @param  boolean            $desc
     * @return Cycle
     * @throws UnderflowException
     * @see Edge::getFirst() for parameters $by and $desc
     * @uses Cycle::factoryFromVertices()
     */
    public static function factoryFromPredecessorMap($predecessors, $vertex, $by = Edge::ORDER_FIFO, $desc = false)
    {
        /*$checked = array();
        foreach ($predecessors as $vertex) {
            $vid = $vertex->getId();
            if (!isset($checked[$vid])) {

            }
        }*/

        // find a vertex in the cycle
        $vid = $vertex->getId();
        $startVertices = array();
        do {
            $startVertices[$vid] = $vertex;

            $vertex = $predecessors[$vid];
            $vid = $vertex->getId();
        } while (!isset($startVertices[$vid]));

        // find negative cycle
        $vid = $vertex->getId();
        // build array of vertices in cycle
        $vertices = array();
        do {
            // add new vertex to cycle
            $vertices[$vid] = $vertex;

            // get predecessor of vertex
            $vertex = $predecessors[$vid];
            $vid = $vertex->getId();
        // continue until we find a vertex that's already in the circle (i.e. circle is closed)
        } while (!isset($vertices[$vid]));

        // reverse cycle, because cycle is actually built in opposite direction due to checking predecessors
        $vertices = array_reverse($vertices, true);

        return Cycle::factoryFromVertices($vertices, $by, $desc);
    }

    /**
     * create new cycle instance with edges between given vertices
     *
     * @param  Vertex[]           $vertices
     * @param  int                $by
     * @param  boolean            $desc
     * @return Cycle
     * @throws UnderflowException if no vertices were given
     * @see Edge::getFirst() for parameters $by and $desc
     */
    public static function factoryFromVertices($vertices, $by = Edge::ORDER_FIFO, $desc = false)
    {
        $edges = array();
        $first = NULL;
        $last = NULL;
        foreach ($vertices as $vertex) {
            // skip first vertex as last is unknown
            if ($first === NULL) {
                $first = $vertex;
            } else {
                // pick edge between last vertex and this vertex
                $edges []= Edge::getFirst($last->getEdgesTo($vertex), $by, $desc);
            }
            $last = $vertex;
        }
        if ($last === NULL) {
            throw new UnderflowException('No vertices given');
        }
        // additional edge from last vertex to first vertex
        $edges []= Edge::getFirst($last->getEdgesTo($first), $by, $desc);

        return new Cycle($vertices, $edges);
    }

    /**
     * create new cycle instance with vertices connected by given edges
     *
     * @param  Edge[] $edges
     * @param  Vertex $startVertex
     * @return Cycle
     */
    public static function factoryFromEdges(array $edges, Vertex $startVertex)
    {
        $vertices = array($startVertex->getId() => $startVertex);
        foreach ($edges as $edge) {
            $vertex = $edge->getVertexToFrom($startVertex);
            $vertices[$vertex->getId()] = $vertex;
            $startVertex = $vertex;
        }

        return new Cycle($vertices, $edges);
    }
}
