<?php

$fields['branches'] = [];

$fields_branches = [];

$fields_branches[] = [ 'id' => 'branch_name',
		            'field_name' => 'name',
		            'label' => [ 'text' => ___( "Branch Name" ) ],
		            'element' => [ 'type' => 'text', 'model' => 'branch.name', 'enter' => "updateBranch()" ],
		            'section' => 'branches' ];

$fields_branches[] = [ 'id' => 'branch_code',
		            'field_name' => 'code',
		            'label' => [ 'text' => ___( "Branch Code" ) ],
		            'element' => [ 'type' => 'text', 'model' => 'branch.code', 'enter' => "updateBranch()" ],
		            'section' => 'branches' ];

$fields_branches[] = [ 'id' => 'branch_address',
		            'field_name' => 'address',
		            'label' => [ 'text' => ___( "Address" ) ],
		            'element' => [ 'type' => 'text', 'model' => 'branch.address', 'enter' => "updateBranch()" ],
		            'section' => 'branches' ];

$fields_branches[] = [ 'id' => 'branch_manager',
		            'field_name' => 'manager',
		            'label' => [ 'text' => ___( "Branch Manager" ) ],
		            'element' => [ 'type' => 'text', 'model' => 'branch.manager', 'enter' => "updateBranch()" ],
		            'section' => 'branches' ];

$fields['branches']['branch'] = $fields_branches;

return $fields;
