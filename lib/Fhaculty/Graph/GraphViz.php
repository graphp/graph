<?php

namespace Fhaculty\Graph;

use Fhaculty\Graph\Algorithm\Groups;
use Fhaculty\Graph\Exception\UnexpectedValueException;
use Fhaculty\Graph\Exception\InvalidArgumentException;
use Fhaculty\Graph\LayoutableInterface;

use \stdClass;

class GraphViz implements LayoutableInterface
{
    /**
     *
     * @var Graph
     */
    private $graph;

    /**
     * file output format to use
     *
     * @var string
     * @see GraphViz::setFormat()
     */
    private $format = 'png';

    private $layoutVertex = array();
    private $layoutEdge = array();

    /**
     * Either the name of full path to GraphViz layout.
     *
     * @var string
     * @see GraphViz::setExecutable()
     */
    private $executable = 'dot';

    const DELAY_OPEN = 2.0;

    const EOL = PHP_EOL;

    public function __construct(Graph $graphToPlot)
    {
        $this->graph = $graphToPlot;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->executable = 'dot.exe';
        }
    }

    /**
     * Change the executable to use.
     *
     * Usually, your graphviz executables should be located in your $PATH
     * environment variable and invoking a mere `dot` is sufficient. If you
     * have no access to your $PATH variable, use this method to set the path
     * to your graphviz dot executable.
     *
     * This should contain '.exe' on windows.
     * - /full/path/to/bin/dot
     * - neato
     * - dot.exe
     * - c:\path\to\bin\dot.exe
     *
     * @param string $executable
     * @return GraphViz $this (chainable)
     */
    public function setExecutable($executable) {
        $this->executable = $executable;

        return $this;
    }

    /**
     * return executable to use
     *
     * @return string
     * @see GraphViz::setExecutable()
     */
    public function getExecutable() {
        return $this->executable;
    }

    /**
     * get original graph (with no layout and styles)
     *
     * @return Graph
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * set graph image output format
     *
     * @param  string   $format png, svg, ps2, etc. (see 'man dot' for details on parameter '-T')
     * @return GraphViz $this (chainable)
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * create and display image for this graph
     *
     * @return void
     * @uses GraphViz::createImageFile()
     */
    public function display()
    {
        // echo "Generate picture ...";
        $tmp = $this->createImageFile();

        static $next = 0;
        if ($next > microtime(true)) {
            // wait some time between calling xdg-open because earlier calls will be ignored otherwise
            echo '[delay flooding xdg-open]' . PHP_EOL;
            sleep(self::DELAY_OPEN);
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            echo "ausgabe\n";
            exec($tmp . ' >NUL');
        } else {
            // open image in background (redirect stdout to /dev/null, sterr to stdout and run in background)
            exec('xdg-open ' . escapeshellarg($tmp) . ' > /dev/null 2>&1 &');

        }

        $next = microtime(true) + self::DELAY_OPEN;
        // echo "... done\n";
    }

    const LAYOUT_GRAPH = 1;
    const LAYOUT_EDGE = 2;
    const LAYOUT_VERTEX = 3;

    private function mergeLayout(&$old, $new)
    {
        if ($new === NULL) {
            $old = array();
        } else {
            foreach ($new as $key => $value) {
                if ($value === NULL) {
                    unset($old[$key]);
                } else {
                    $old[$key] = $value;
                }
            }
        }
    }

    /**
     * set the global layout for GRAPH, EDGE or VERTEX
     *
     * By defining the layout at the graph level we can set in a way
     * default values for all vertices and edges. It is also possible to set
     * values for the graph itself. Ie background color, layout engine, etc.
     *
     * @param type $where
     * @param type $layout
     * @param type $value
     * @return \Fhaculty\Graph\GraphViz
     * @throws InvalidArgumentException
     */
    public function setLayoutBy($where, $layout, $value = NULL)
    {
        if (!is_array($where)) {
            $where = array($where);
        }
        if (func_num_args() > 2) {
            $layout = array($layout => $value);
        }
        foreach ($where as $where) {
            if ($where === self::LAYOUT_GRAPH) {
                $this->setLayout($layout);
            } elseif ($where === self::LAYOUT_EDGE) {
                $this->mergeLayout($this->layoutEdge, $layout);
            } elseif ($where === self::LAYOUT_VERTEX) {
                $this->mergeLayout($this->layoutVertex, $layout);
            } else {
                throw new InvalidArgumentException('Invalid layout identifier');
            }
        }

        return $this;
    }

    // end

    /**
     * create image file data contents for this graph
     *
     * @return string
     * @uses GraphViz::createImageFile()
     */
    public function createImageData()
    {
        $file = $this->createImageFile();
        $data = file_get_contents($file);
        unlink($file);

        return $data;
    }

    /**
     * create base64-encoded image src target data to be used for html images
     *
     * @return string
     * @uses GraphViz::createImageData()
     */
    public function createImageSrc()
    {
        $format = ($this->format === 'svg' || $this->format === 'svgz') ? 'svg+xml' : $this->format;

        return 'data:image/' . $format . ';base64,' . base64_encode($this->createImageData());
    }

    /**
     * create image html code for this graph
     *
     * @return string
     * @uses GraphViz::createImageSrc()
     */
    public function createImageHtml()
    {
        if ($this->format === 'svg' || $this->format === 'svgz') {
            return '<object type="image/svg+xml" data="' . $this->createImageSrc() . '"></object>';
        }

        return '<img src="' . $this->createImageSrc() . '" />';
    }

    /**
     * create image file for this graph
     *
     * @return string                   filename
     * @throws UnexpectedValueException on error
     * @uses GraphViz::createScript()
     */
    public function createImageFile()
    {
        $script = $this->createScript();
        // var_dump($script);

        $tmp = tempnam(sys_get_temp_dir(), 'graphviz');
        if ($tmp === false) {
            throw new UnexpectedValueException('Unable to get temporary file name for graphviz script');
        }

        $ret = file_put_contents($tmp, $script, LOCK_EX);
        if ($ret === false) {
            throw new UnexpectedValuexception('Unable to write graphviz script to temporary file');
        }

        $ret = 0;

        $executable = $this->getExecutable();
        system($executable . ' -T ' . escapeshellarg($this->format) . ' ' . escapeshellarg($tmp) . ' -o ' . escapeshellarg($tmp . '.' . $this->format), $ret);
        if ($ret !== 0) {
            throw new UnexpectedValueException('Unable to invoke "' . $executable .'" to create image file (code ' . $ret . ')');
        }

        unlink($tmp);

        return $tmp . '.' . $this->format;
    }

    /**
     * create graphviz script representing this graph
     *
     * @return string
     * @uses Graph::isDirected()
     * @uses Graph::getVertices()
     * @uses Graph::getEdges()
     */
    public function createScript()
    {
        $directed = $this->graph->isDirected();

        $script = ($directed ? 'di':'') . 'graph G {' . self::EOL;

        // add global attributes
        $layout = $this->getLayout();
        if ($layout) {
            $script .= '  graph ' . $this->escapeAttributes($layout) . self::EOL;
        }
        if ($this->layoutVertex) {
            $script .= '  node ' . $this->escapeAttributes($this->layoutVertex) . self::EOL;
        }
        if ($this->layoutEdge) {
            $script .= '  edge ' . $this->escapeAttributes($this->layoutEdge) . self::EOL;
        }

        $alg = new Groups($this->graph);
        // only append group number to vertex label if there are at least 2 different groups
        $showGroups = ($alg->getNumberOfGroups() > 1);

        if ($showGroups) {
            $gid = 0;
            // put each group of vertices in a separate subgraph cluster
            foreach ($alg->getGroups() as $group) {
                $script .= '  subgraph cluster_' . $gid++ . ' {' . self::EOL .
                           '    label = ' . $this->escape($group) . self::EOL;
                foreach($alg->getVerticesGroup($group) as $vid => $vertex) {
                    $layout = $vertex->getLayout();

                    $balance = $vertex->getBalance();
                    if($balance !== NULL){
                        if($balance > 0){
                            $balance = '+' . $balance;
                        }
                        if(!isset($layout['label'])){
                            $layout['label'] = $vid;
                        }
                        $layout['label'] .= ' (' . $balance . ')';
                    }

                    $script .= '    ' . $this->escapeId($vid);
                    if($layout){
                        $script .= ' ' . $this->escapeAttributes($layout);
                    }
                    $script .= self::EOL;
                }
                $script .= '  }' . self::EOL;
            }
        } else {
            // explicitly add all isolated vertices (vertices with no edges) and vertices with special layout set
            // other vertices wil be added automatically due to below edge definitions
            foreach ($this->graph->getVertices() as $vid => $vertex){
                $layout = $vertex->getLayout();

                $balance = $vertex->getBalance();
                if($balance !== NULL){
                    if($balance > 0){
                        $balance = '+' . $balance;
                    }
                    if(!isset($layout['label'])){
                        $layout['label'] = $vid;
                    }
                    $layout['label'] .= ' (' . $balance . ')';
                }

                if($vertex->isIsolated() || $layout){
                    $script .= '  ' . $this->escapeId($vid);
                    if($layout){
                        $script .= ' ' . $this->escapeAttributes($layout);
                    }
                    $script .= self::EOL;
                }
            }
        }

        $edgeop = $directed ? ' -> ' : ' -- ';

        // add all edges as directed edges
        foreach ($this->graph->getEdges() as $currentEdge) {
            $both = $currentEdge->getVertices();
            $currentStartVertex = $both[0];
            $currentTargetVertex = $both[1];

            $script .= '  ' . $this->escapeId($currentStartVertex->getId()) . $edgeop . $this->escapeId($currentTargetVertex->getId());

            $attrs = $currentEdge->getLayout();

            // use flow/capacity/weight as edge label
            $label = NULL;

            $flow = $currentEdge->getFlow();
            $capacity = $currentEdge->getCapacity();
            // flow is set
            if ($flow !== NULL) {
                // NULL capacity = infinite capacity
                $label = $flow . '/' . ($capacity === NULL ? '∞' : $capacity);
            // capacity set, but not flow (assume zero flow)
            } elseif ($capacity !== NULL) {
                $label = '0/' . $capacity;
            }

            $weight = $currentEdge->getWeight();
            // weight is set
            if ($weight !== NULL) {
                if ($label === NULL) {
                    $label = $weight;
                } else {
                    $label .= '/' . $weight;
                }
            }

            if ($label !== NULL) {
                $attrs['label'] = $label;
            }
            // this edge also points to the opposite direction => this is actually an undirected edge
            if ($directed && $currentEdge->isConnection($currentTargetVertex, $currentStartVertex)) {
                $attrs['dir'] = 'none';
            }
            if ($attrs) {
                $script .= ' ' . $this->escapeAttributes($attrs);
            }

            $script .= self::EOL;
        }
        $script .= '}' . self::EOL;

        return $script;
    }

    /**
     * escape given id string and wrap in quotes if needed
     *
     * @param  string $id
     * @return string
     * @link http://graphviz.org/content/dot-language
     */
    private function escapeId($id)
    {
        return self::escape($id);
    }

    public static function escape($id)
    {
        // see raw()
        if ($id instanceof stdClass && isset($id->string)) {
            return $id->string;
        }
        // see @link: There is no semantic difference between abc_2 and "abc_2"
        // numeric or simple string, no need to quote (only for simplicity)
        if (preg_match('/^(?:\-?(?:\.\d+|\d+(?:\.\d+)?))$/i', $id)) {
            return $id;
        }

        return '"' . str_replace(array('&', '<', '>', '"', "'", '\\', "\n"), array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '\\\\', '\\l'), $id) . '"';
    }

    /**
     * get escaped attribute string for given array of (unescaped) attributes
     *
     * @param  array  $attrs
     * @return string
     * @uses GraphViz::escapeId()
     */
    private function escapeAttributes($attrs)
    {
        $script = '[';
        $first = true;
        foreach ($attrs as $name => $value) {
            if ($first) {
                $first = false;
            } else {
                $script .= ' ';
            }
            $script .= $name . '=' . self::escape($value);
        }
        $script .= ']';

        return $script;
    }

    /**
     * create a raw string representation, i.e. do NOT escape the given string when used in graphviz output
     *
     * @param  string   $string
     * @return StdClass
     * @see GraphViz::escape()
     */
    public static function raw($string)
    {
        return (object) array('string' => $string);
    }

    /**
     * associative array of layout settings
     *
     * @var array
     */
    private $layout = array();

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout(array $attributes)
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

    public function setLayoutAttribute($name, $value)
    {
        if ($value === NULL) {
            unset($this->layout[$name]);
        } else {
            $this->layout[$name] = $value;
        }

        return $this;
    }

    public function hasLayoutAttribute($name)
    {
        return isset($this->layout[$name]);
    }

    public function getLayoutAttribute($name)
    {
        if (!isset($this->layout[$name])) {
            throw new OutOfBoundsException('Given layout attribute is not set');
        }

        return $this->layout[$name];
    }


}
