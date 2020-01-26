<?php

$fields['roles'] = [];

$fields_roles = [];

$fields_roles[] = [ 'id' => 'role_name',
            'field_name' => 'name',
            'label' => [ 'text' => ___( "Name" ) ],
            'element' => [ 'type' => 'text', 'model' => 'role.name', 'enter' => "updateBranch()" ],
            'section' => 'roles' ];

$fields_roles[] = [ 'id' => 'role_description',
            'field_name' => 'description',
            'label' => [ 'text' => ___( "Description" ) ],
            'element' => [ 'type' => 'text', 'model' => 'role.description', 'enter' => "updateBranch()" ],
            'section' => 'roles' ];

$fields['roles']['role'] = $fields_roles;

return $fields;
