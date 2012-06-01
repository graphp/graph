<?php

abstract class Layoutable {
    /**
     * associative array of layout settings
     * 
     * @var array
     */
    private $layout = array();
    
    /**
     * get array of layout settings
     * 
     * @return array
     */
    public function getLayout(){
        return $this->layout;
    }
    
    /**
     * set raw layout without applying escaping rules
     * 
     * @param string|array $name
     * @param mixed        $value
     * @return Layoutable|Graph|Vertex|Edge $this (chainable)
     */
    public function setLayoutRaw($name,$value=NULL){
        if($name === NULL){
            $this->layout = array();
            return $this;
        }
        if(!is_array($name)){
            $name = array($name=>$value);
        }
        foreach($name as $key=>$value){
            if($value === NULL){
                unset($this->layout[$key]);
            }else{
                $this->layout[$key] = $value;
            }
        }
        return $this;
    }
    
    /**
     * set layout option
     * 
     * @param string|array $name
     * @param mixed        $value
     * @return Layoutable|Graph|Vertex|Edge $this (chainable)
     * @uses GraphViz::escape()
     */
    public function setLayout($name,$value=NULL){
        if($name === NULL){
            $this->layout = array();
            return $this;
        }
        if(!is_array($name)){
            $name = array($name=>$value);
        }
        foreach($name as $key=>$value){
            if($value === NULL){
                unset($this->layout[$key]);
            }else{
                $this->layout[$key] = GraphViz::escape($value);
            }
        }
        return $this;
    }
    
    /**
     * checks whether layout option with given name is set
     * 
     * @param string $name
     * @return boolean
     */
    public function hasLayout($name){
        return isset($this->layout[$name]);
    }
}
