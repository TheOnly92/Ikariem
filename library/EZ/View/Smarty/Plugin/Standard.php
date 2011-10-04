<?php

class EZ_View_Smarty_Plugin_Standard extends EZ_View_Smarty_Plugin_Abstract {
 
	public static function layoutFunction($params, &$smarty) {
		$section = $params ['section'];
		$toReturn = "";
		try {
			//oppress errors
			@$toReturn = $smarty->_tpl_vars ['this']->placeholder ( 'Zend_Layout' )->$section;
			return $toReturn;
		} catch ( Exception $e ) {
			return $toReturn;
		}
	}
 
	public static function headTitleFunction($params, &$smarty) {
		$method = (isset ( $params ['method'] )) ? $params ['method'] : - 1;
		unset ( $params ['method'] );
		$args = (isset ( $params ['args'] )) ? self::parseArgs ( $params ['args'] ) : null;
		$toReturn = "";
		try {
			//oppress errors
			if ($method != - 1) {
				@$toReturn = $smarty->_tpl_vars ['this']->headTitle ()->$method ( $args );
			} else {
				$toReturn = $smarty->_tpl_vars ['this']->headTitle ();
			}
			return $toReturn;
		} catch ( Exception $e ) {
			return $toReturn;
		}
	}
 
	public static function headScriptFunction($params, &$smarty) {
		$method = (isset ( $params ['method'] )) ? $params ['method'] : - 1;
		unset ( $params ['method'] );
		$args = (isset ( $params ['args'] )) ? self::parseArgs ( $params ['args'] ) : null;
		$toReturn = "";
		try {
			if ($method != - 1) {
				//oppress errors
				if (is_array ( $args )) {
					//$toReturn = call_user_func('$smarty->_tpl_vars["this"]->headScript()->$method()',$args,true);
					//print "args src= ".$args['src'];
					//TODO: fix args usage
					$toReturn = $smarty->_tpl_vars ['this']->headScript ()->$method ( $args ['src'] );
				} else {
					@$toReturn = $smarty->_tpl_vars ['this']->headScript ()->$method ( $args );
				}
			} else {
				$toReturn = $smarty->_tpl_vars ['this']->headScript ();
			}
			return $toReturn;
		} catch ( Exception $e ) {
			return $toReturn;
		}
	}
 
	public static function jsLocaleFunction($params, &$smarty) {
		$method = (isset ( $params ['method'] )) ? $params ['method'] : - 1;
		unset ( $params ['method'] );
		$args = (isset ( $params ['args'] )) ? self::parseArgs ( $params ['args'] ) : null;
		$toReturn = "";
		try {
			if ($method != - 1) {
				//oppress errors
				if (is_array ( $args )) {
					//$toReturn = call_user_func('$smarty->_tpl_vars["this"]->headScript()->$method()',$args,true);
					//print "args src= ".$args['src'];
					//TODO: fix args usage
					$toReturn = $smarty->_tpl_vars ['this']->jsLocale ()->$method ( $args ['src'] );
				} else {
					@$toReturn = $smarty->_tpl_vars ['this']->jsLocale ()->$method ( $args );
				}
			} else {
				$toReturn = $smarty->_tpl_vars ['this']->jsLocale ();
			}
			return $toReturn;
		} catch ( Exception $e ) {
			return $toReturn;
		}
	}
 
	public static function headStyleFunction($params, &$smarty) {
		$method = (isset ( $params ['method'] )) ? $params ['method'] : - 1;
		unset ( $params ['method'] );
		$args = (isset ( $params ['args'] )) ? self::parseArgs ( $params ['args'] ) : null;
		$toReturn = "";
		try {
			if ($method != - 1) {
				//oppress errors
				if (is_array ( $args )) {
					//$toReturn = call_user_func('$smarty->_tpl_vars["this"]->headStyle()->$method()',$args,true);
					//print "args src= ".$args['src'];
					//TODO: fix args usage
					$toReturn = $smarty->_tpl_vars ['this']->headStyle ()->$method ( $args ['src'] );
				} else {
					@$toReturn = $smarty->_tpl_vars ['this']->headStyle ()->$method ( $args );
				}
			} else {
				$toReturn = $smarty->_tpl_vars ['this']->headStyle ();
			}
			return $toReturn;
		} catch ( Exception $e ) {
			return $toReturn;
		}
	}
 
	public static function parseArgs($args) {
		if (preg_match_all ( "/([^\\[^\\]]+)/", $args, $matches )) {
			$params = explode ( ',', $matches [1] [0] );
			$toReturn = array ( );
			foreach ( $params as $p ) {
				@list ( $key, $value ) = explode ( '=>', $p );
				$toReturn [preg_replace ( "/[\\' ]+/", "", $key )] = preg_replace ( "/[\\' ]+/", "", $value );
			}
			return $toReturn;
		}
		return $args;
	}
 
	/**
	 * Smarty block function, provides gettext support for smarty.
	 *
	 * The block content is the text that should be translated.
	 *
	 * Any parameter that is sent to the function will be represented as %n in the translation text,
	 * where n is 1 for the first parameter. The following parameters are reserved:
	 *   - escape - sets escape mode:
	 *       - 'html' for HTML escaping, this is the default.
	 *       - 'js' for javascript escaping.
	 *       - 'url' for url escaping.
	 *       - 'no'/'off'/0 - turns off escaping
	 *   - plural - The plural version of the text (2nd parameter of ngettext())
	 *   - count - The item count for plural mode (3rd parameter of ngettext())
	 */
	public static function tBlock($params, $text, &$smarty) {
		$text = stripslashes ( $text );
 
		// set escape mode
		if (isset ( $params ['escape'] )) {
			$escape = $params ['escape'];
			unset ( $params ['escape'] );
		}
 
		// set plural version
		if (isset ( $params ['plural'] )) {
			$plural = $params ['plural'];
			unset ( $params ['plural'] );
 
			// set count
			if (isset ( $params ['count'] )) {
				$count = $params ['count'];
				unset ( $params ['count'] );
			}
		}
 
		// use plural if required parameters are set
		if (isset ( $count ) && isset ( $plural )) {
			$text = EZ_Language::getInstance ()->translatePlural ( $text, $plural, $count );
		} else { // use normal
			$text = EZ_Language::getInstance ()->translate ( $text );
		}
 
		// run strarg if there are parameters
		if (count ( $params )) {
			$text = self::strarg ( $text, $params );
		}
 
		if (! isset ( $escape ) || $escape == 'html') { // html escape, default
			$text = nl2br ( htmlspecialchars ( $text ) );
		} elseif (isset ( $escape )) {
			switch ( $escape) {
				case 'javascript' :
				case 'js' :
					// javascript escape
					$text = str_replace ( '\'', '\\\'', stripslashes ( $text ) );
				break;
				case 'url' :
					// url escape
					$text = urlencode ( $text );
				break;
			}
		}
 
		return $text;
	}
 
/**
 * Replaces arguments in a string with their values.
 * Arguments are represented by % followed by their number.
 *
 * @param	string	Source string
 * @param	mixed	Arguments, can be passed in an array or through single variables.
 * @returns	string	Modified string
 */
	public static function strarg($str) {
	$tr = array();
	$p = 0;
 
	for ($i=1; $i < func_num_args(); $i++) {
		$arg = func_get_arg($i);
 
		if (is_array($arg)) {
			foreach ($arg as $aarg) {
				$tr['%'.++$p] = $aarg;
			}
		} else {
			$tr['%'.++$p] = $arg;
		}
	}
 
	return strtr($str, $tr);
  }
}
