<?php

namespace Fhaculty\Graph\Exception;

use Fhaculty\Graph;

class NegativeCycleException extends \Exception implements Graph\Exception{
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
