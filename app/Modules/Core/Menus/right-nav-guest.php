<?php

/*

	@As of V1.0.0
	$menu_items contains the menu items. Each menu item needs at minimum, a URL, and text. If you add an 'items' key to an item, the items in that key become sub-menus. Right now, only 2 levels are supported.

*/

$menus = [];

$right_nav = [];

$right_nav["1"] = [ 'id' => 'main-menu-right_nav-login', 
										'url' => '/login', 
										'text' => "<i class='fa fa-sign-in-alt'></i> &nbsp; " . ___('Log In'), 
										'visibility' => 'guest', ];
$right_nav["2"] = [ 'id' => 'main-menu-right_nav-register', 
										'url' => '/register', 
										'text' => "<i class='fa fa-user-circle'></i> &nbsp; " . ___('Register'), 
										'visibility' => 'guest', ];
$right_nav["3"] = [ 'id' => 'main-menu-right_nav-password', 
										'url' => '/password/reset',
										 'text' => "<i class='fa fa-key'></i> &nbsp; " . ___('Recover Password'), 
										 'visibility' => 'guest', ];

$menus['right_nav'] = $right_nav;

return $menus;
