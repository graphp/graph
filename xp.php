#!/usr/bin/php
<?php

class Graph{
    private $edges = array();
    
    private $vertices = array();
    
    
    public function getEdge($id){
        if(!isset($edges[$id])){
            throw new Exception();
        }
        return $edges[$id];
    }
    
    public function addEdge($id=NULL){
        return new Edge($id);
    }
    
    public function getMatrixOb(){ }
    
    public function breitensuche(){
        $alg = new BreitenSuche_Agl($this);
        return $alg->getResult();
    }
    
    public function isConsecutive(){
        $is = 0;
    }
}


class Loader{
     public function loadMatrix($file){
         $graph = new Graph();
         
         $lines = file($file);
         $graph->addEdge();
         $graph->addVertice();
         
         return $graph;   
    }
}


function main(){
    
    $graph = Loader::loadMatrix('demo.txt');
    
    $edge = $graph->getEdge('A');
    
    echo $edge->breitensuche();
}

echo 1;
echo 2;
echo 3;
// main();
sleep(3);
usleep(300000);
