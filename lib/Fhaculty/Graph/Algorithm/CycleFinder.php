<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\ShortestPath\Dijkstra;
use Fhaculty\Graph\Algorithm\StronglyConnectedComponents\Tarjan;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Graph;

/**
 * This algorithm search in o(E+V) time the shortest path inside a
 * strongly connected graph.
 *
 * You can use Tarjan Algorithm to find such graphs.
 * @see http://stackoverflow.com/questions/10456935/graph-how-to-find-minimum-directed-cycle-minimum-total-weight
 */
class CycleFinder extends BaseGraph
{
    public function getShortestCycle()
    {
        $walkMap = array();
        $vertices = $this->graph->getVertices()->getList();

        // Compute distance map between all pairs.
        foreach($vertices as $u){
            foreach($vertices as $v){
                if($u === $v){
                    continue;
                }
                if(isset($walkMap[$u->getId()][$v->getId()])){
                    continue;
                }
                $distUV = new Dijkstra($u);
                $walkMap[$u->getId()][$v->getId()] = $distUV->getWalkTo($v);
            }
        }

        // The eulerian path is the longest possible, so we init the index with it +1.
        $minimum = count($this->graph->getEdges()) + 1;
        $walk = null;

        // Find shortest
        foreach($vertices as $u){
            foreach($vertices as $v){
                if($u === $v){
                    continue;
                }
                $walkU = $walkMap[$u->getId()][$v->getId()];
                $walkV = $walkMap[$v->getId()][$u->getId()];
                $length = $walkU->getLength() + $walkV->getLength();

                if($length < $minimum){
                    $minimum = $length;
                    $walk = $walkU->append($walkV);
                }
            }
        }

        return $walk;
    }
}
