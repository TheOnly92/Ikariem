<?php

/**
 * EZ_View_Smarty_Plugin_Broker
 *
 * This class registers smarty plugins with the current smarty view
 */
class EZ_View_Smarty_Plugin_Broker {
 
	/**
	 * Array of instance of objects extending EZ_View_Smarty_Plugin_Abstract
	 *
	 * @var array
	 */
	protected $_plugins = array ( );
	protected $_view;
 
	/**
	 *
	 * @param EZ_View_Smarty $view
	 */
	public function __construct($view) {
		$this->_view = $view;
	}
 
	/**
	 * Register a plugin.
	 *
	 * @param  EZ_View_Smarty_Plugin_Abstract $plugin
	 * @param  int $stackIndex
	 * @return EZ_View_Smarty_Plugin_Broker
	 */
	public function registerPlugin(EZ_View_Smarty_Plugin_Abstract $plugin,$stackIndex = null) {
		if (false !== array_search ( $plugin, $this->_plugins, true )) {
			throw new EZ_View_Smarty_Exception ( 'Plugin already registered' );
		}
 
		$stackIndex = ( int ) $stackIndex;
 
		if ($stackIndex) {
			if (isset ( $this->_plugins [$stackIndex] )) {
				throw new EZ_View_Smarty_Exception ( 'Plugin with stackIndex "' . $stackIndex . '" already registered' );
			}
			$this->_plugins [$stackIndex] = $plugin;
		} else {
			$stackIndex = count ( $this->_plugins );
			while ( isset ( $this->_plugins [$stackIndex] ) ) {
				++ $stackIndex;
			}
			$this->_plugins [$stackIndex] = $plugin;
		}
 
		ksort ( $this->_plugins );
		$this->reLoadPlugins();
		return $this;
	}
 
	/**
	 * Unregister a plugin.
	 *
	 * @param string|EZ_View_Smarty_Plugin_Abstract $plugin Plugin object or class name
	 * @return EZ_View_Smarty_Plugin_Broker
	 */
	public function unregisterPlugin($plugin) {
		if ($plugin instanceof EZ_View_Smarty_Plugin_Abstract) {
			// Given a plugin object, find it in the array
			$key = array_search ( $plugin, $this->_plugins, true );
			if (false === $key) {
				throw new EZ_View_Smarty_Exception ( 'Plugin never registered.' );
			}
			unset ( $this->_plugins [$key] );
		} elseif (is_string ( $plugin )) {
			// Given a plugin class, find all plugins of that class and unset them
			foreach ( $this->_plugins as $key => $_plugin ) {
				$type = get_class ( $_plugin );
				if ($plugin == $type) {
					unset ( $this->_plugins [$key] );
				}
			}
		}
		$this->reLoadPlugins();
		return $this;
	}
 
	/**
	 * Is a plugin of a particular class registered?
	 *
	 * @param  string $class
	 * @return bool
	 */
	public function hasPlugin($class) {
		$found = array ( );
		foreach ( $this->_plugins as $plugin ) {
			$type = get_class ( $plugin );
			if ($class == $type) {
				return true;
			}
		}
 
		return false;
	}
 
	/**
	 * Retrieve a plugin or plugins by class
	 *
	 * @param  string $class Class name of plugin(s) desired
	 * @return false|EZ_View_Smarty_Plugin_Abstract|array Returns false if none found, plugin if only one found, and array of plugins if multiple plugins of same class found
	 */
	public function getPlugin($class) {
		$found = array ( );
		foreach ( $this->_plugins as $plugin ) {
			$type = get_class ( $plugin );
			if ($class == $type) {
				$found [] = $plugin;
			}
		}
 
		switch ( count ( $found )) {
			case 0 :
				return false;
			case 1 :
				return $found [0];
			default :
				return $found;
		}
	}
 
	/**
	 * Retrieve all plugins
	 *
	 * @return array
	 */
	public function getPlugins() {
		return $this->_plugins;
	}
 
	/**
	 * Load all plugins
	 */
	public function loadPlugins() {
		foreach($this->_plugins as $plugin) {
			$type = get_class($plugin);
			//get an array of all the classfunctions that should be mapped to smarty functions
			$functions = $plugin->getClassFunctionArray($type);
			foreach($functions as $key => $value) {
				if(preg_match('/Block$/',$value)) {
					$this->_view->getEngine()->register_block($key,$value);
				} else {
					$this->_view->getEngine()->register_function($key,$value);
				}
			}
 
		}
	}
 
	public function unLoadPlugins() {
		foreach($this->_plugins as $plugin) {
			$type = get_class($plugin);
			//get an array of all the classfunctions that should be mapped to smarty functions
			$functions = $plugin->getClassFunctionArray($type);
			foreach($functions as $key => $value) {
				if(preg_match('/Block$/',$value)) {
					$this->_view->getEngine()->unregister_block($key);
				} else {
					$this->_view->getEngine()->unregister_function($key);
				}
			}
 
		}
	}
 
	public function reLoadPlugins() {
		$this->unLoadPlugins();
		$this->loadPlugins();
	}
}
