<?php

/**
 * 
 * @author clue
 * @link http://www.ffnn.nl/pages/articles/media/uml-diagrams-using-graphviz-dot.php
 * @link http://www.holub.com/goodies/uml/
 */
class LoaderUmlClassDiagram extends Loader{
    private $graph;
    public function __construct(){
        $this->graph = new Graph();
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
        
        $defaults = $reflection->getDefaultProperties();
        foreach($reflection->getProperties() as $property){
            if($property->getDeclaringClass()->getName() !== $class) continue;
            
            $label .= $this->visibility($property);
            if($property->isStatic()){
            	$label .= ' «static»';
            }
            $label .= ' ' . $this->escape($property->getName());
            
            $type = NULL; // TODO: parse docblock for parameter type
            if($type !== NULL){
            	$label .= ' : '.$type;
            }
            
            if(isset($defaults[$property->getName()])){ // only show non-NULL values
                $label .= ' = '.$this->getCasted($defaults[$property->getName()]);
            }
            
            $label .= '\\l';
        }
        
        $label .= '|';
        
        foreach($reflection->getMethods() as $method){
            if($method->getDeclaringClass()->getName() !== $class) continue; // method not defined in this class (inherited from parent), so skip
            
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
                
                $type = NULL; // TODO: parse docblock for parameter type
                if($type !== NULL){
                    $label .= ' : '.$type;
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
            
            $type = NULL; // TODO: parse docblock for return value
            if($type !== NULL){
                $label .= ' : '.$type;
            }
            
            $label .= '\\l'; // align this line to the left
        }
        
        $label .= '}"';
        
        $vertex->setLayout('shape','record');
        $vertex->setLayoutRaw('label',$label);
        return $vertex;
    }
    
    public function getGraph(){
        return $this->graph;
    }
    
    private function getCasted($value){
        if($value === NULL){
        	return 'NULL';
        }else if(is_string($value)){
        	return '\\"'.$this->escape(str_replace('"','\\"',$value)).'\\"';
        }else if(is_bool($value)){
        	return $value ? 'true' : 'false';
        }else if(is_int($value) || is_float($value)){
        	return $value;
        }else if(is_array($value)){
            if($value === array()){
                return '[]';
            }else{
                return '[…]';
            }
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
