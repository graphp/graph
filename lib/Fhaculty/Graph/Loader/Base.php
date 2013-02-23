<?php

namespace Fhaculty\Graph\Loader;

abstract class Base
{
    private $debugMode = false;

    protected $directedEdges = false;

    abstract public function createGraph();

    public function setEnableDirectedEdges($directedEdges)
    {
        $this->directedEdges = $directedEdges;
    }

    protected function writeDebugMessage($messageString)
    {
        if ($this->debugMode) {
            echo $messageString;
        }

    }
}
