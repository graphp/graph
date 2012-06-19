<?php

namespace Fhaculty\Graph\Exception;

use Fhaculty\Graph\Cycle;

use Fhaculty\Graph;

class NegativeCycleException extends UnexpectedValueException implements Graph\Exception{
	/**
	 * instance of the cycle
	 * 
	 * @var Cycle
	 */
    private $cycle;

	public function __construct($message,$code=NULL,$previous=NULL,Cycle $cycle){
		parent::__construct($message,$code,$previous);
		$this->cycle = $cycle;
	}
	
	/**
	 * 
	 * @return Cycle
	 */
	public function getCycle(){
	    return $this->cycle;
	}
}
