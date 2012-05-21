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
     * @param string $name
     * @param mixed  $value
     * @return Layoutable|Graph|Vertex|Edge $this (chainable)
     */
    public function setLayoutRaw($name,$value){
    	if($value === NULL){
    		unset($this->layout[$name]);
    	}else{
    		$this->layout[$name] = $value;
    	}
    	return $this;
    }
    
    /**
     * set layout option
     * 
     * @param string $name
     * @param mixed  $value
     * @return Layoutable|Graph|Vertex|Edge $this (chainable)
     * @uses GraphViz::escape()
     * @uses Layoutable::setLayoutRaw()
     */
    public function setLayout($name,$value){
    	return $this->setLayoutRaw($name,GraphViz::escape($value));
    }
}
