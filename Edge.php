<?php

abstract class Edge{	
	abstract public function hasVertexTo($vertex);
	
	abstract public function hasVertexFrom($vertex);
	
	abstract public function getVerticesTo();
	
	abstract public function getVerticesFrom();
}
