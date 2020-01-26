<?php

/*

	@As of V1.0.0
	$menu_items contains the menu items. Each menu item needs at minimum, a URL, and text. If you add an 'items' key to an item, the items in that key become sub-menus. Right now, only 2 levels are supported.

*/

$links = [];

$links_company = [];

$links_company["1"] = [ 'id' => 'admin-portal-company-information', 
										'url' => '/settings/company', 
										'icon' => "fa fa-building",
										'title' => ___('Setup'), 
										'module' => 'Core',
										'description' => ___('Edit information stored on the company, including name, officers, and contact information.')
									];

$links_company["2"] = [ 'id' => 'admin-portal-company-branding', 
										'url' => '/settings/branding', 
										'icon' => "fa fa-paint-brush",
										'title' => ___('Branding'), 
										'module' => 'Core',
										'description' => ___('Modify colour schemes, logo and maintain branding of your company throughout the app.')
									];

$links_company["3"] = [ 'id' => 'admin-portal-company-branches', 
										'url' => '/settings/branches', 
										'icon' => "fa fa-sitemap",
										'title' => ___('Branches'), 
										'module' => 'Core',
										'description' => ___('Configure countless for your company, both physical and virtual.')
									];

$links['links_company'] = [ "1" => [ 'title' => ___( 'Company' ), 'items' => $links_company ] ];

$links_security = [];

$links_security["1"] = [ 'id' => 'admin-portal-security-roles', 
										'url' => '/settings/roles', 
										'icon' => "fa fa-lock",
										'title' => ___('Roles &amp; Permissions'), 
										'module' => 'Core',
										'description' => ___('Set up specific user roles and assign default permissions.')
									];

$links_security["2"] = [ 'id' => 'admin-portal-security-people', 
										'url' => '/settings/people', 
										'icon' => "fa fa-users",
										'title' => ___('People'), 
										'module' => 'Core',
										'description' => ___('Manage people, their information, access rights and more.')
									];

$links['links_security'] = [ "1" => [ 'title' => ___( 'Security' ), 'items' => $links_security ] ];

$links_general = [];

$links_general["1"] = [ 'id' => 'admin-portal-lists-manager', 
										'url' => '/settings/lists', 
										'icon' => "fa fa-list-alt",
										'title' => ___('Manage Lists'), 
										'module' => 'Core',
										'description' => ___('List manager is a cool feature that allows you to manage any number of lists, with each item having as many fields as you like.')
									];

$links['links_general'] = [ "1" => [ 'title' => ___( 'General' ), 'items' => $links_general ] ];

return $links;
