<?php

/**
 * 
 * @author clue
 * @link http://www.ffnn.nl/pages/articles/media/uml-diagrams-using-graphviz-dot.php
 * @link http://www.holub.com/goodies/uml/
 */
class LoaderUmlClassDiagram extends Loader{
    private $graph;
    
    private $options = array(
		'only-self'   => true, // whether to only show methods/properties that are actually defined in this class (and not those merely inherited from base)
		'only-public' => false, // whether to only show public methods/properties (or also include private/protected ones)
        'show-constants' => true, // whether to show class constants as readonly static variables (or just omit them completely)
    );
    
    public function __construct(){
        $this->graph = new Graph();
    }
    
    public function setOption($name,$flag){
    	if(!isset($this->options[$name])){
    		throw new Exception('Invalid option name "'.$name.'"');
    	}
    	$this->options[$name] = !!$flag;
    	return $this;
    }
    
    public function hasClass($class){
        try{
            $this->graph->getVertex($class);
            return true;
        }
        catch(Exception $ignroe){}
        return false;
    }
    
    public function createVertexClass($class){
        $vertex = $this->graph->createVertex($class);
        
        $reflection = new ReflectionClass($class);
        
        $parent = $reflection->getParentClass();
        if($parent){
            try{
                $parentVertex = $this->graph->getVertex($parent->getName());
            }
            catch(Exception $ignore){
                $parentVertex = $this->createVertexClass($parent->getName());
            }
            $vertex->createEdgeTo($parentVertex)->setLayout('arrowhead','empty');
        }
        
        foreach($reflection->getInterfaceNames() as $interface){
            try{
            	$parentVertex = $this->graph->getVertex($interface);
            }
            catch(Exception $ignore){
            	$parentVertex = $this->createVertexClass($interface);
            }
            $vertex->createEdgeTo($parentVertex)->setLayout('arrowhead','empty')->setLayout('style','dashed');
        }
        
        $label = '"{';
        
        $isInterface = false;
        if($reflection->isInterface()){
            $label .= '«interface»\\n';
            $isInterface = true;
        }else if($reflection->isAbstract()){
            $label .= '«abstract»\\n';
        }
        
        $label .= $this->escape($class).'|';
        
        if($this->options['show-constants']){
        	foreach($reflection->getConstants() as $name=>$value){
        		if($this->options['only-self'] && $parent && $parent->getConstant($name) === $value) continue;
        
        		$label .= '+ «static» '.self::escape($name).' : '.$this->escape($this->getType(gettype($value))).' = '.$this->getCasted($value).' \\{readOnly\\}\\l';
        	}
        }
        
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property){
            if($this->options['only-self'] && $property->getDeclaringClass()->getName() !== $class) continue;
            
            if($this->options['only-public'] && !$property->isPublic()) continue;
            
            $label .= $this->visibility($property);
            if($property->isStatic()){
            	$label .= ' «static»';
            }
            $label .= ' ' . $this->escape($property->getName());
            
            $type = $this->getDocBlockVar($property);
            if($type !== NULL){
            	$label .= ' : '.$this->escape($type);
            }
            
            if(isset($defaults[$property->getName()])){ // only show non-NULL values
                $label .= ' = '.$this->getCasted($defaults[$property->getName()]);
            }
            
            $label .= '\\l';
        }
        
        $label .= '|';
        
        foreach($reflection->getMethods() as $method){
            if($this->options['only-self'] && $method->getDeclaringClass()->getName() !== $class) continue; // method not defined in this class (inherited from parent), so skip
            
            if($this->options['only-public'] && !$method->isPublic()) continue;
            
//             $ref = preg_replace('/[^a-z0-9]/i','',$method->getName());
//             $label .= '<"'.$ref.'">';
            
            $label .= $this->visibility($method);
            
            if(!$isInterface && $method->isAbstract()){
            	$label .= ' «abstract»';
            }
            if($method->isStatic()){
            	$label .= ' «static»';
            }
            $label .= ' ' . $this->escape($method->getName()).'(';
            
            $firstParam = true;
            foreach($method->getParameters() as $parameter){
                if($firstParam){
                    $firstParam = false;
                }else{
                    $label .= ', ';
                }
                
                $label .= $this->escape($parameter->getName());
                
                $type = $this->getParameterType($parameter);
                if($type !== NULL){
                    $label .= ' : '.$this->escape($type);
                }
                
                if($parameter->isOptional()){
                    try{
                        $label .= ' = '.$this->getCasted($parameter->getDefaultValue());
                    }
                    catch(Exception $ignore){
                        $label .= ' = «unknown»';
                    }
                }
            }
            $label .= ')';
            
            $type = $this->getDocBlockReturn($method);
            if($type !== NULL){
                $label .= ' : '.$this->escape($type);
            }
            
            $label .= '\\l'; // align this line to the left
        }
        
        $label .= '}"';
        
        $vertex->setLayout('shape','record');
        $vertex->setLayoutRaw('label',$label);
        return $vertex;
    }
    
    /**
     * create new uml note (attached to given class vertex)
     * 
     * @param string      $note
     * @param Vertex|NULL $for
     * @return LoaderUmlClassDiagram $this (chainable)
     */
    public function createVertexNote($note,$for=NULL){
    	$vertex = $this->graph->createVertex()->setLayout('label',$note."\n")
                                        	  ->setLayout('shape','note')
                                          	  ->setLayout('fontsize',8)
                                        	  //->setLayout('margin','0 0')
                                        	  ->setLayout('style','filled')
                                        	  ->setLayout('fillcolor','yellow');
    
    	if($for !== NULL){
    		$vertex->createEdgeTo($for)->setLayout('len',1)
    		->setLayout('style','dashed')
    		->setLayout('arrowhead','none');
    	}
    	return $vertex;
    }
    
    public function getGraph(){
        return $this->graph;
    }
    
    private function getDocBlock($ref){
    	$doc = $ref->getDocComment();
    	if($doc !== false){
    		return trim(preg_replace('/(^(?:\h*\*)\h*|\h+$)/m','',substr($doc,3,-2)));
    	}
    	return NULL;
    }
    
    private function getDocBlockVar($ref){
    	return $this->getType($this->getDocBlockSingle($ref,'var'));
    }
    
    private function getDocBlockReturn($ref){
    	return $this->getType($this->getDocBlockSingle($ref,'return'));
    }
    
    private function getParameterType(ReflectionParameter $parameter){
    	$class = $parameter->getClass();
    	if($class !== NULL){
    		return $class->getName();
    	}
    
    	$pos = $parameter->getPosition();
    	$refFn = $parameter->getDeclaringFunction();
    	$params = $this->getDocBlockMulti($refFn,'param');
    	if(count($params) === $refFn->getNumberOfParameters()){
    		return $this->getType($params[$pos]);
    	}
    	return NULL;
    }
    
    private function getDocBlockMulti($ref,$what){
    	$doc = $this->getDocBlock($ref);
    	if($doc === NULL){
    		//return 'nah';
    		return NULL;
    	}
    	preg_match_all('/^@'.$what.' ([^\s]+)/m',$doc,$matches,PREG_SET_ORDER);
    	$ret = array();
    	foreach($matches as $match){
    		$ret []= trim($match[1]);
    	}
    	return $ret;
    }
    
    private function getDocBlockSingle($ref,$what){
    	$multi = $this->getDocBlockMulti($ref, $what);
    	if(count($multi) !== 1){
    		//return json_encode($matches);
    		return NULL;
    	}
    	return $multi[0];
    }
    
    private function getType($ret){
        if($ret === NULL){
            return NULL;
        }
        if(preg_match('/^array\[(\w+)\]$/',$ret,$match)){
        	return $this->getType($match[1]).'[]';
        }
        if(!preg_match('/^\w+$/',$ret)){
        	return 'mixed';
        }
        if($ret === 'integer'){
            $ret = 'int';
        }else if($ret === 'double'){
            $ret = 'float';
        }else if($ret === 'boolean'){
            return 'bool';
        }
        return $ret;
    }
    
    /**
     * get given value casted to string (and escaped in double quotes it needed)
     * 
     * @param mixed $value
     * @return string
     * @uses LoaderUmlClassDiagram::escape()
     */
    private function getCasted($value){
        if($value === NULL){
        	return 'NULL';
        }else if(is_string($value)){
        	return '\\"'.$this->escape(str_replace('"','\\"',$value)).'\\"';
        }else if(is_bool($value)){
        	return $value ? 'true' : 'false';
        }else if(is_int($value) || is_float($value)){
        	return (string)$value;
        }else if(is_array($value)){
            if($value === array()){
                return '[]';
            }else{
                return '[…]';
            }
        }else if(is_object($var)){
            return get_class($var).'{…}';
        }
        return '…';
    }
    
    private function visibility($ref){
        if($ref->isPublic()){
        	return '+';
        }else if($ref->isProtected()){
        	return '#';
        }else if($ref->isPrivate()){
        	return "\342\200\223"; // U+2013 EN DASH "–"
        }
        return '?';
    }
    
    private function escape($id){
        return preg_replace('/([^\\w])/u','\\\\$1',str_replace(array("\r","\n","\t"),array('\\r','\\n','\\t'),$id));
    }
}
