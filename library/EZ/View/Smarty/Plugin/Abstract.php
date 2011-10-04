<?php

abstract class EZ_View_Smarty_Plugin_Abstract {
 
	protected $_functionRegistry;
	protected $_namingPattern ='/([a-zA-Z1-9]+)(Function|Block)$/';
 
	/**
	 *
	 */
	function __construct() {
 
	}
 
	/**
	 * @return array
	 */
	public function getClassFunctionArray() {
		$type = get_class($this);
		$methods = get_class_methods($this);
		foreach($methods as $value) {
			if(preg_match($this->_namingPattern,$value,$matches)) {
				$this->_functionRegistry[$matches[1]] = $type."::".$value;
			}
		}
		return $this->_functionRegistry;
	}
 
	/**
	 * change the default naming pattern for functions that should be mapped to smarty functions
	 */
	public function setNamingPattern($pattern ='/([a-zA-Z1-9]+)(Function|Block)$/') {
		 //"/([a-zA-Z1-9]+)Function$/";
		 $this->_namingPattern = $pattern;
	}
}
