<?php

class GraphViz{
    private $graph;
    private $attributes = array();
    
    /**
     * file output format to use
     * 
     * @var string
     * @see GraphViz::setFormat()
     */
    private $format = 'png';
    
    const EOL = PHP_EOL;
    
	public function __construct(Graph $graphToPlot){
		$this->graph = $graphToPlot;
	}
	
	/**
	 * set graph image output format
	 * 
	 * @param string $format png, svg, ps2, etc. (see 'man dot' for details on parameter '-T') 
	 * @return GraphViz $this (chainable)
	 */
	public function setFormat($format){
	    $this->format = $format;
	    return $this;
	}
	
	/**
	 * create and display image for this graph
	 * 
	 * @return void
	 * @uses GraphViz::createImageFile()
	 */
	public function display(){
        //echo "Generate picture ...";
        $tmp = $this->createImageFile();
        
        static $next = 0;
        if($next > microtime(true)){
            echo '[delay flooding xdg-open]'; // wait some time between calling xdg-open because earlier calls will be ignored otherwise
            sleep(1);
        }
        exec('xdg-open '.escapeshellarg($tmp).' > /dev/null 2>&1 &'); // open image in background (redirect stdout to /dev/null, sterr to stdout and run in background)
        $next = microtime(true) + 1.0;
        //echo "... done\n";
	}
	
	public function setAttribute($where,$name,$value=NULL){
	    if($where === 'vertex'){
	        $where = 'node';
	    }
	    if(is_array($name)){
	        foreach($name as $name=>$value){
	            $this->attributes[$where][$name] = $value;
	        }
	        return $this;
	    }
	    $this->attributes[$where][$name] = $value;
	    return $this;
	}
	
	/**
	 * create base64-encoded image src target data to be used for html images
	 * 
	 * @return string
	 * @uses GraphViz::createImageData()
	 */
	public function createImageSrc(){
	    $format = ($this->format === 'svg' || $this->format === 'svgz') ? 'svg+xml' : $this->format;
	    return 'data:image/'.$format.';base64,'.base64_encode($this->createImageData());
	}
	
	/**
	 * create image html code for this graph
	 * 
	 * @return string
	 * @uses GraphViz::createImageSrc()
	 */
	public function createImageHtml(){
	    if($this->format === 'svg' || $this->format === 'svgz'){
	        return '<object type="image/svg+xml" data="'.$this->createImageSrc().'"></object>';
	    }
	    return '<img src="'.$this->createImageSrc().'" />';
	}
	
	/**
	 * create image file data contents for this graph
	 * 
	 * @return string
	 * @uses GraphViz::createImageFile()
	 */
	public function createImageData(){
	    $file = $this->createImageFile();
	    $data = file_get_contents($file);
	    unlink($file);
	    return $data;
	}
	
	/**
	 * create image file for this graph
	 * 
	 * @return string filename
	 * @throws Exception on error
	 * @uses GraphViz::createScript()
	 */
	public function createImageFile(){
        $script = $this->createScript();
	    //var_dump($script);
	    
	    $tmp = tempnam('/tmp','graphviz');
	    if($tmp === false){
	        throw new Exception('Unable to get temporary file name for graphviz script');
	    }
	    
	    $ret = file_put_contents($tmp.'.gv',$script,LOCK_EX);
	    if($ret === false){
	        throw new Exception('Unable to write graphviz script to temporary file');
	    }
	    
	    $ret = 0;
	    system('dot -T '.escapeshellarg($this->format).' '.escapeshellarg($tmp.'.gv').' -o '.escapeshellarg($tmp.'.'.$this->format),$ret); // use program 'dot' to actually generate graph image
	    if($ret !== 0){
	        throw new Exception('Unable to invoke "dot" to create image file (code '.$ret.')');
	    }
	    
	    unlink($tmp.'.gv');
	    
	    return $tmp.'.'.$this->format;
	}
	
	/**
	 * create graphviz script representing this graph
	 * 
	 * @return string
	 * @uses Graph::isDirected()
	 * @uses Graph::getVertices()
	 * @uses Graph::getEdges()
	 */
	public function createScript(){
	    $directed = $this->graph->isDirected();
	    
		$script = ($directed ? 'di':'') . 'graph G {'.self::EOL;
		
		// add global attributes
		foreach(array('graph','node','edge') as $where){
    		if(isset($this->attributes[$where])){
    		    $script .= '  ' . $where . ' ' . $this->escapeAttributes($this->attributes[$where]) . self::EOL;
    		}
		}
		
		// explicitly add all isolated vertices (vertices with no edges)
		// other vertices wil be added automatically due to below edge definitions
		foreach ($this->graph->getVertices() as $vertex){
		    if($vertex->isIsolated()){
		        $script .= '  ' . $this->escapeId($vertex->getId()) . self::EOL;
		    }
		}
		
		$edgeop = $directed ? ' -> ' : ' -- ';
		
		// add all edges as directed edges
		foreach ($this->graph->getEdges() as $currentEdge){
		    $both = $currentEdge->getVertices();
		    $currentStartVertex = $both[0];
		    $currentTargetVertex = $both[1];
		    
		    $script .= '  ' . $this->escapeId($currentStartVertex->getId()) . $edgeop . $this->escapeId($currentTargetVertex->getId());
	        
		    $attrs = array();
		    
    	    $weight = $currentEdge->getWeight();
    	    if($weight !== NULL){                                       // add weight as label (if set)
    	        $attrs['label']  = $weight;
     	        $attrs['weight'] = $weight;
    	    }
    	    // this edge also points to the opposite direction => this is actually an undirected edge
    	    if($directed && $currentEdge->isConnection($currentTargetVertex,$currentStartVertex)){
    	        $attrs['dir'] = 'none';
    	    }
    	    if($attrs){
    	        $script .= ' '.$this->escapeAttributes($attrs);
    	    }
    	    
    	    $script .= self::EOL;
		}
		$script .= '}'.self::EOL;
	    return $script;
	}
	
	/**
	 * escape given id string and wrap in quotes if needed
	 * 
	 * @param string $id
	 * @return string
	 * @link http://graphviz.org/content/dot-language
	 */
	private function escapeId($id){
	    // see @link: There is no semantic difference between abc_2 and "abc_2"
	    if(preg_match('/^(?:\-?(?:\.\d+|\d+(?:\.\d+)?)|[a-z_][a-z0-9_]*)$/i',$id)){ // numeric or simple string, no need to quote (only for simplicity)
	        return $id;
	    }
	    return '"'.str_replace(array('&','<','>','"',"'",'\\'),array('&amp;','&lt;','&gt;','&quot;','&apos;','\\\\'),$id).'"';
	}
	
	/**
	 * get escaped attribute string for given array of (unescaped) attributes
	 * 
	 * @param array $attrs
	 * @return string
	 * @uses GraphViz::escapeId()
	 */
	private function escapeAttributes($attrs){
        $script = '[';
        $first = true;
        foreach($attrs as $name=>$value){
            if($first){
                $first = false;
            }else{
                $script .= ' ';
            }
            $script .= $name.'='.$this->escapeId($value);
        }
        $script .= ']';
	    return $script;
	}
}