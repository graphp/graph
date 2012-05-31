<?php

abstract class LoaderFile extends Loader{
    protected $fileName;
    
    public function __construct($filename){
        $this->fileName = $filename;
    }
    
    protected function getLines(){
        $ret = file($this->fileName, FILE_IGNORE_NEW_LINES);
        if($ret === false){
            throw new Exception('Unable to read file');
        }
        return $ret;
    }
}
