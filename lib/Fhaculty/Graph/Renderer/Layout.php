<?php

namespace Fhaculty\Graph\Renderer;

use Fhaculty\Graph\Exception\OutOfBoundsException;
use Fhaculty\Graph\LayoutableInterface;

class Layout
{
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
    public function getAttributes()
    {
        return $this->layout;
    }

    /**
     * set multiple layout attributes
     *
     * @param  array $attributes
     * @return self  $this (chainable)
     * @see Layoutable::setAttribute()
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if ($value === NULL) {
                unset($this->layout[$key]);
            } else {
                $this->layout[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * set a single layout attribute
     *
     * @param  string $name
     * @param  string $value
     * @return self
     * @see Layoutable::setAttributes()
     */
    public function setAttribute($name, $value)
    {
        if ($value === NULL) {
            unset($this->layout[$name]);
        } else {
            $this->layout[$name] = $value;
        }

        return $this;
    }

    /**
     * checks whether layout option with given name is set
     *
     * @param  string  $name
     * @return boolean
     */
    public function hasAttribute($name)
    {
        return isset($this->layout[$name]);
    }

    public function getAttribute($name)
    {
        if (!isset($this->layout[$name])) {
            throw new OutOfBoundsException('Given layout attribute is not set');
        }

        return $this->layout[$name];
    }

    public function setLayout(self $layout)
    {
        $this->layout = $layout->getAttributes();

        return $this;
    }
}
