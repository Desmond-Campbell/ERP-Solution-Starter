<?php

$fields['people'] = [];

# Personal Info

	$fields_personal = [];

	$fields_personal[] = [ 'id' => 'person_first_name',
	                          'field_name' => 'first_name',
	                          'label' => [ 'text' => ___( "First Name" ) ],
	                          'element' => [ 'type' => 'text', 'model' => 'person.first_name', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_middle_name',
	                          'field_name' => 'middle_name',
	                          'label' => [ 'text' => ___( "Middle Name" ) ],
	                          'element' => [ 'type' => 'text', 'model' => 'person.middle_name', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_last_name',
	                          'field_name' => 'last_name',
	                          'label' => [ 'text' => ___( "Last Name" ) ],
	                          'element' => [ 'type' => 'text', 'model' => 'person.last_name', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_email',
	                          'field_name' => 'email',
	                          'label' => [ 'text' => ___( "Email" ) ],
	                          'element' => [ 'type' => 'text', 'model' => 'person.email', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_gender',
	                          'field_name' => 'gender',
	                          'label' => [ 'text' => ___( "Gender" ) ],
	                          'options' => [ [ 'value' => "'male'", 'label' => 'Male' ], [ 'value' => "'female'", 'label' => 'Female' ] ],
	                          'element' => [ 'type' => 'radio', 'model' => 'person.gender', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_dob',
	                          'field_name' => 'dob',
	                          'label' => [ 'text' => ___( "Date of Birth" ) ],
	                          'element' => [ 'type' => 'date', 'model' => 'person.dob', 'enter' => "updatePerson()" ],
	                          'section' => 'person' ];

	$fields_personal[] = [ 'id' => 'person_about',
	                          'field_name' => 'about',
	                          'label' => [ 'text' => ___( "About" ) ],
	                          'element' => [ 'type' => 'textarea', 'model' => 'person.about', 'enter' => "updatePerson()", 'cols' => 6 ],
	                          'section' => 'person' ];

	$fields['people']['personal'] = $fields_personal;

return $fields;