<?php

namespace Fhaculty\Graph\Loader;

use Fhaculty\Graph\Graph;
use \Exception;

abstract class File extends Base{
    protected $fileName;
    
    public function __construct($filename){
        $this->fileName = $filename;
    }
    
    /**
     * get an array of all lines in this file
     * 
     * @return array[string]
     * @throws Exception if file can not be read
     */
    protected function getLines(){
        $ret = file($this->fileName);
        if($ret === false){
            throw new Exception('Unable to read file');
        }
        $lines = array();
        foreach($ret as $line){
            $lines[] = rtrim($line);
        }
        return $lines;
    }
    
    /**
     * read integer value for given line string
     * 
     * @param string $line
     * @return int
     * @throws Exception
     */
    protected function readInt($line){
        if((string)(int)$line !== $line){
            throw new Exception('Invalid integer');
        }
        return (int)$line;
    }
    
    /**
     * read float value for given line string
     *
     * @param string $line
     * @return float
     * @throws Exception
     */
    protected function readFloat($line){
        if((string)(float)$line != $line){
            throw new Exception('Invalid float');
        }
        return (float)$line;
    }
    
    /**
     * read given line string into given array of parts (int, float, vertex) 
     * 
     * @param string $line
     * @param array  $parts
     * @param Graph  $graph
     * @return array[mixed]
     * @throws Exception
     */
    protected function readLine($line,$parts,Graph $graph=NULL){
        $ret = array();
        $explode = explode("\t",$line);
        $i = 0;
        foreach($parts as $key=>$part){
            if(!isset($explode[$i])){
                throw new Exception('Line does not split into enough parts');
            }
            $value = $explode[$i++];
            if($part === 'int'){
                $value = $this->readInt($value);
            }else if($part === 'float'){
                $value = $this->readFloat($value);
            }else if($part === 'vertex'){
                $value = $graph->getVertex($value);
            }else{
                throw new Exception('Invalid type "'.$part.'"');
            }
            $ret[$key] = $value;
        }
        return $ret;
    }
}
