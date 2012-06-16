<?php

namespace Fhaculty\Graph;

use \Exception;

class NegativeCycleException extends Exception{
	/**
	 * instance of the cycle
	 * 
	 * @var Cycle
	 */
    private $cycle;

	public function __construct($message,$cycle){
		parent::__construct($message,NULL,NULL);
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
