<?php

abstract class LoaderFile implements Loader{
	
	protected $fileName;
	
	public function setFileName($fileName){
		$this->fileName = $fileName;
	}
}