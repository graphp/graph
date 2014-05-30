<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\ShortestPath\Dijkstra;
use Fhaculty\Graph\Algorithm\StronglyConnectedComponents\Tarjan;
use Fhaculty\Graph\Walk;
use Fhaculty\Graph\Graph;

class CycleFinder extends BaseGraph
{
    /**
     * @see (not used) http://stones333.blogspot.fr/2013/12/find-cycles-in-directed-graph-dag.html
     * @see http://stackoverflow.com/questions/10456935/graph-how-to-find-minimum-directed-cycle-minimum-total-weight
     */
    public function getShortestCycle()
    {
        $walkMap = array();
        $vertices = $this->graph->getVertices()->getList();

        // Compute distance map
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

        // The eulerian path is the shortest
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