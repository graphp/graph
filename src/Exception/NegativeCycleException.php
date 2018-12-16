<?php

namespace Fhaculty\Graph\Exception;

use Fhaculty\Graph\Walk;
use Fhaculty\Graph;

class NegativeCycleException extends UnexpectedValueException implements Graph\Exception
{
    /**
     * Instance of the cycle
     *
     * @var Walk|null
     */
    private $cycle;

    /**
     * NegativeCycleException constructor.
     *
     * @param string $message
     * @param int $code
     * @param null $previous
     * @param \Fhaculty\Graph\Walk|NULL $cycle
     */
    public function __construct($message, $code = 0, $previous = null, Walk $cycle = null)
    {
        parent::__construct($message, $code, $previous);
        $this->cycle = $cycle;
    }

    /**
     * Get the cycle.
     *
     * @return Walk|null
     */
    public function getCycle()
    {
        return $this->cycle;
    }
}
