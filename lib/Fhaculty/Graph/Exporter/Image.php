<?php

namespace Fhaculty\Graph\Exporter;

use Fhaculty\Graph\GraphViz;
use Fhaculty\Graph\Graph;

class Image implements ExporterInterface
{
    private $format = 'png';

    public function getOutput(Graph $graph)
    {
        $graphviz = new GraphViz($graph);
        $graphviz->setFormat($this->format);
        return $graphviz->createImageData();
    }

    /**
     * set the image output format to use
     *
     * @param string $type png, svg
     * @return self $this (chainable)
     * @see GraphViz::setFormat()
     */
    public function setFormat($type)
    {
        $this->format = $type;
        return $this;
    }
}
