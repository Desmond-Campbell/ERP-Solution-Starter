<?php

$fields['account'] = [];

$fields_personal = [];

$fields_personal[] = [ 'id' => 'account_personal_first_name',
			  'field_name' => 'first_name',
			  'label' => [ 'text' => ___( "First Name" ) ],
			  'element' => [ 'type' => 'text', 'model' => 'account.first_name', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_middle_name',
			  'field_name' => 'middle_name',
			  'label' => [ 'text' => ___( "Middle Name" ) ],
			  'element' => [ 'type' => 'text', 'model' => 'account.middle_name', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_last_name',
			  'field_name' => 'last_name',
			  'label' => [ 'text' => ___( "Last Name" ) ],
			  'element' => [ 'type' => 'text', 'model' => 'account.last_name', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_email',
			  'field_name' => 'email',
			  'label' => [ 'text' => ___( "Email" ) ],
			  'element' => [ 'type' => 'text', 'model' => 'account.email', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_gender',
			  'field_name' => 'gender',
			  'label' => [ 'text' => ___( "Gender" ) ],
			  'options' => [ [ 'value' => "'male'", 'label' => 'Male' ], [ 'value' => "'female'", 'label' => 'Female' ] ],
			  'element' => [ 'type' => 'radio', 'model' => 'account.gender', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_dob',
			  'field_name' => 'dob',
			  'label' => [ 'text' => ___( "Date of Birth" ) ],
			  'element' => [ 'type' => 'date', 'model' => 'account.dob', 'enter' => "updateAccount()" ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields_personal[] = [ 'id' => 'account_personal_about',
			  'field_name' => 'about',
			  'label' => [ 'text' => ___( "About" ) ],
			  'element' => [ 'type' => 'textarea', 'model' => 'account.about', 'enter' => "updateAccount()", 'cols' => 6 ],
			  'section' => 'account_personal',
			  'position' => 0 ];

$fields['account']['personal'] = $fields_personal;

return $fields;
