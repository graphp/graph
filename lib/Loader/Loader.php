<?php

abstract class Loader{
    
    private $debugMode = false;
    
    protected $directedEdges = false;
    
    public abstract function createGraph();
    
    public function setEnableDirectedEdges($directedEdges){
        $this->directedEdges = $directedEdges;
    }
    
    protected function writeDebugMessage($messageString){
        if($this->debugMode){
            echo $messageString;
        }
    
    }
}
