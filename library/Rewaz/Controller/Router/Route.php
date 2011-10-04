<?php
/**
 * Rewaz Labs Production
 * Ikariem
 * Router file.
 * Initialize everything and do the routing.
 * Last updated: $LastChangedDate$
 * 
 * @author		TheOnly92
 * @copyright	(c) 2008-2010 Rewaz Labs.
 * @package		Project Chamista
 * @version		$LastChangedRevision$
 */

class Rewaz_Controller_Router_Route extends Zend_Controller_Router_Route {
	public function assemble($data = array(), $reset = false, $encode = false, $partial = false) {
		if ($this->_isTranslated) {
            $translator = $this->getTranslator();

            if (isset($data['@locale'])) {
                $locale = $data['@locale'];
                unset($data['@locale']);
            } else {
                $locale = $this->getLocale();
            }
        }
        
        $url  = array();
        $flag = false;
        
        $return = '';
        $query = '';
        
		foreach ($this->_parts as $key => $part) {
            $name = isset($this->_variables[$key]) ? $this->_variables[$key] : null;
			
            $useDefault = false;
            if (isset($name) && array_key_exists($name, $data) && $data[$name] === null) {
                $useDefault = true;
            }

            if (isset($name)) {
                if (isset($data[$name]) && !$useDefault) {
                    $value = $data[$name];
                    unset($data[$name]);
                } elseif (!$reset && !$useDefault && isset($this->_values[$name])) {
                    $value = $this->_values[$name];
                } elseif (!$reset && !$useDefault && isset($this->_wildcardData[$name])) {
                    $value = $this->_wildcardData[$name];
                } elseif (isset($this->_defaults[$name])) {
                    $value = $this->_defaults[$name];
                } else {
                    require_once 'Zend/Controller/Router/Exception.php';
                    throw new Zend_Controller_Router_Exception($name . ' is not specified');
                }

                if ($this->_isTranslated && in_array($name, $this->_translatable)) {
                    $return .= $translator->translate($value, $locale).'/';
                } else {
                    $return .= $value.'/';
                }
            } else {
                if (!$reset) $data += $this->_wildcardData;
                $defaults = $this->getDefaults();
                foreach ($data as $var => $value) {
                    if ($value !== null && (!isset($defaults[$var]) || $value != $defaults[$var]) && !empty($value)) {
                        $query .= $var.'='.$value.'&';
                        $flag = true;
                    }
                }
            }
        }
        $query = substr($query,0,-1);
        $return = substr($return,0,-1);
        if (!empty($query))
        	$return .= '?'.$query;
        
        return trim($return, $this->_urlDelimiter);
	}
}