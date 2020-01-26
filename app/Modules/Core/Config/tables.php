<?php

$columns = [];

$columns['branches'] = [];
$columns['branches'][] = [ 'id' => 'name', 'title' => ___( 'Branch Name' ) ];
$columns['branches'][] = [ 'id' => 'code', 'title' => ___( 'Branch Code' ) ];
$columns['branches'][] = [ 'id' => 'address', 'title' => ___( 'Address' ) ];
$columns['branches'][] = [ 'id' => 'manager', 'title' => ___( 'Manager' ) ];

$columns['people'] = [];
$columns['people'][] = [ 'id' => 'first_name', 'title' => ___( 'First Name' ) ];
$columns['people'][] = [ 'id' => 'last_name', 'title' => ___( 'Last Name' ) ];
$columns['people'][] = [ 'id' => 'email', 'title' => ___( 'Email Address' ) ];

$columns['roles'] = [];
$columns['roles'][] = [ 'id' => 'name', 'title' => ___( 'Role Name' ) ];
$columns['roles'][] = [ 'id' => 'permissions_summary', 'title' => ___( 'Permissions' ) ];

return $columns;
