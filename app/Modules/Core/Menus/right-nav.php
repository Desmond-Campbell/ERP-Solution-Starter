<?php

/*

	@As of V1.0.0
	$menu_items contains the menu items. Each menu item needs at minimum, a URL, and text. If you add an 'items' key to an item, the items in that key become sub-menus. Right now, only 2 levels are supported.

*/

$menus = [];

$right_nav = [];

$right_nav["1"] = [ 'id' => 'main-menu-right_nav-account', 
										'url' => '/account', 
										'type' => 'dropdown-item',
										'text' => ___('Manage Account'), 
										'visibility' => "auth", ];

$right_nav["2"] = [ 'id' => 'main-menu-right_nav-logout', 
										'type' => 'dropdown-item',
										'url' => 'javascript:document.logout.submit()', 
										'text' => ___('Log out'), 
										'visibility' => "auth", ];

$menus['right_nav'] = $right_nav;

return $menus;
