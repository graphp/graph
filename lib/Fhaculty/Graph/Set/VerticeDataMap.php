<?php


namespace Fhaculty\Graph\Set;

class VerticeDataMap implements \ArrayAccess
{
    private $dataMap = array();

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->dataMap[$offset->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->dataMap[$offset->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->dataMap[$offset->getId()] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->dataMap[$offset->getId()]);
    }

}
