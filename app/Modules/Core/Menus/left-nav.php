<?php

/*

	@As of V1.0.0
	$menu_items contains the menu items. Each menu item needs at minimum, a URL, and text. If you add an 'items' key to an item, the items in that key become sub-menus. Right now, only 2 levels are supported.

*/

$menus = [];

$left_nav = [];

$left_nav["1"] = [ 'id' => 'main-menu-settings-home',
										'url' => '/settings', 
										'hover' => ___('Go to Settings Portal'), 
										'text' => '<i class="fa fa-th"></i>', 
										'visibility' => "auth" ];

$left_nav["2"] = [ 	'id' => 'main-menu-settings',
										'url' => '#', 
										'text' => '<i class=\'fa fa-cog\'></i>',
										'items' => [ 
												"1" => [ 'url' => '/settings/lists', 'text' => ___('List Manager') ],
											],
										'visibility' => "auth", 
					 ];

$left_nav["4"] = [ 'id' => 'main-menu-company',
										'url' => '#', 'text' => ___( "Company" ),
										'items' => [ 
												"1" => [ 'url' => '/settings/company', 'text' => ___('Company Information') ],
												"2" => [ 'url' => '/settings/branches', 'text' => ___('Branches') ],
												"3" => [ 'type' => 'divider' ] /* Use type: divider to insert a horizontal diver on a sub-menu */,
												"4" => [ 'url' => '/settings/branding', 'text' => ___('Branding') ],
											], 
										'visibility' => "auth",
								 ];

$left_nav["5"] = [ 'id' => 'main-menu-security',
										'url' => '#', 'text' => ___( "Security" ),
										'items' => [ 
												"1" => [ 'url' => '/settings/roles', 'text' => ___('Roles &amp; Permissions') ],
												"2" => [ 'url' => '/settings/people', 'text' => ___('Manage People') ],
											],
										'visibility' => "auth",
								 ];

$left_nav["6"] = [ 'id' => 'main-menu-search', 
								'url' => '/search', 
								'text' => '<i class=\'fa fa-search\'></i>', 
								'visibility' => "auth" ];

$menus['left_nav'] = $left_nav;

return $menus;
