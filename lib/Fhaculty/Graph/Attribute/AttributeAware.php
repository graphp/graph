<?php

namespace Fhaculty\Graph\Attribute;

/**
 * Implemented by any entity that is aware of additional attributes
 *
 * Each attribute consists of a name (string) and an arbitrary value.
 */
interface AttributeAware
{
    /**
     * get a single attribute with the given $name
     *
     * @param string $name
     * @return mixed|null
     */
    public function getAttribute($name);

    /**
     * set a single attribute with the given $name to given $value
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute($name, $value);

    /**
     * get a container for all attributes
     *
     * @return AttributeBag
     */
    public function getAttributeBag();
}
