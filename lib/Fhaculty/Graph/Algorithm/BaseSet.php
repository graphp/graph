<?php

namespace Fhaculty\Graph\Algorithm;

use Fhaculty\Graph\Algorithm\Base;
use Fhaculty\Graph\Set;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Walk;

/**
 * Abstract base class for algorithms that operate on a given Set instance
 *
 * @see Set
 */
abstract class BaseSet extends Base
{
    /**
     * Set to operate on
     *
     * @var Set
     */
    protected $set;

    /**
     * instantiate new algorithm
     *
     * @param Graph|Walk|Set $graphOrWalk either the Graph or Walk to operate on (or the common base class Set)
     */
    public function __construct(Set $graphOrWalk)
    {
        $this->set = $graphOrWalk;
    }
}
