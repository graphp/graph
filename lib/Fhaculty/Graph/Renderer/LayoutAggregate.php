<?php

namespace Fhaculty\Graph\Renderer;

use Fhaculty\Graph\Exception\OutOfBoundsException;

interface LayoutAggregate
{
    /**
     *
     * @return Layout
     */
    public function getLayout();
}
