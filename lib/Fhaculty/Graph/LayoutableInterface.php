<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Exception\OutOfBoundsException;

interface LayoutableInterface
{
    public function getLayout();

    /**
     * set multiple layout attributes
     *
     * @param  array $attributes
     * @return self  $this (chainable)
     * @see Layoutable::setLayoutAttribute()
     */
    public function setLayout(array $attributes);

    /**
     * set a single layouto attribute
     *
     * @param  string $name
     * @param  string $value
     * @return self
     * @see Layoutable::setLayout()
     */
    public function setLayoutAttribute($name, $value);

    /**
     * checks whether layout option with given name is set
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasLayoutAttribute($name);

    public function getLayoutAttribute($name);
}
