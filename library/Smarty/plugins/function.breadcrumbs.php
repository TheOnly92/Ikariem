<?php

function smarty_function_breadcrumbs($params, &$smarty) {
	if (isset ( $params ['trail'] ) && is_object ( $params ['trail'] )) {
		$Trailer = &$params ['trail'];
		$trail = $Trailer->path;
	} else {
		$trail = array ();
	}
	
	if (isset ( $params ['separator'] ))
		$separator = $params ['separator'];
	else
		$separator = ' &gt; ';
	
	if (isset($params['length']))
		$length = ( int ) $params ['length'];
	else
		$length = 0;
	
	$links = array ();
	
	$trailSize = count ( $trail );
	if ($trailSize > 2) {
		$trail[0]['title'] = '<img src="/img/layout/icon-world.gif" alt="World" />';
		$trail[1]['class'] = 'island';
	}
	if ($trailSize > 3) {
		$island_name = explode(' ',$trail[1]['title']);
		$trail[1]['title'] = $island_name[1];
		$trail[2]['class'] = 'city';
	}
	if ($trailSize > 4) {
		$trail[2]['title'] = '<img src="/img/layout/icon-city2.gif" alt="'.$trail[2]['title'].'" />';
		$trail[2]['class'] = '';
	}
	
	for($i = 0; $i < $trailSize; $i ++) {
		if ($length > 0) {
			require_once $smarty->_get_plugin_filepath ( 'modifier', 'truncate' );
			$title = smarty_modifier_truncate ( $trail [$i] ['title'], $length );
		} else
			$title = $trail [$i] ['title'];
		
		$link = '';
		if (isset($trail[$i]['link'])) {
			$link .= '<a href="'.$trail[$i]['link'].'"';
			if (isset($trail[$i]['tips'])) {
				$link .= ' title="'.$trail[$i]['tips'].'"';
			}
			if (isset($trail[$i]['class'])) {
				$link .= ' class="'.$trail[$i]['class'].'"';
			}
			$link .= '>'.$title.'</a>';
		} else {
			$link = $title;
		}
		
		$links[] = $link;
	}
	
	return $bread = join ( $separator . "\n", $links );;
}