<?php

$fields['company'] = [];

# Details

	$fields_details = [];
	
	$fields_details[] = [ 'id' => 'company_details_name',
	                            'field_name' => 'name',
	                            'label' => [ 'text' => ___( "Company Name" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'company.name', 'enter' => "updateCompany('company_details')" ],
	                            'section' => 'company_details' ];

	$fields_details[] = [ 'id' => 'company_details_type',
	                            'field_name' => 'type',
	                            'label' => [ 'text' => ___( "Business Type" ) ],
	                            'element' => [ 'type' => 'select', 'model' => 'company.type', 'enter' => "updateCompany('company_details')", 'attributes' => [ ':options' => 'lists.company_types' ] ],
	                            'section' => 'company_details' ];

	$fields_details[] = [ 'id' => 'company_details_tax_id',
	                            'field_name' => 'tax_id',
	                            'label' => [ 'text' => ___( "Taxpayer Number" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'company.tax_id', 'enter' => "updateCompany('company_details')" ],
	                            'section' => 'company_details' ];

	$fields_details[] = [ 'id' => 'company_details_licence_number',
	                            'field_name' => 'licence_number',
	                            'label' => [ 'text' => ___( "Licence Number" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'company.licence_number', 'enter' => "updateCompany('company_details')" ],
	                            'section' => 'company_details' ];

	$fields['company']['details'] = $fields_details;

# Addresses

	$fields_addresses = [];
	
	$fields_addresses[] = [ 'id' => 'company_address_details',
	                            'field_name' => 'address',
	                            'label' => [ 'text' => ___( "Address Details" ) ],
	                            'element' => [ 'type' => 'textarea', 'model' => 'edit_item.address.address', 'enter' => "changeItem('address')" ],
	                            'section' => 'company_addresses' ];

	$fields_addresses[] = [ 'id' => 'company_address_type',
	                            'field_name' => 'type',
	                            'label' => [ 'text' => ___( "Address Type" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'edit_item.address.type', 'enter' => "changeItem('address')" ],
	                            'section' => 'company_addresses' ];

	$fields['company']['addresses'] = $fields_addresses;

# Phone Numbers

	$fields_phone_numbers = [];
	
	$fields_phone_numbers[] = [ 'id' => 'company_phone_number',
	                            'field_name' => 'number',
	                            'label' => [ 'text' => ___( "Phone Number" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'edit_item.phone_number.number', 'enter' => "changeItem('phone_number')" ],
	                            'section' => 'company_phone_numbers' ];

	$fields_phone_numbers[] = [ 'id' => 'company_phone_type',
	                            'field_name' => 'type',
	                            'label' => [ 'text' => ___( "Type" ) ],
	                            'element' => [ 'type' => 'text', 'model' => 'edit_item.phone_number.type', 'enter' => "changeItem('phone_number')" ],
	                            'section' => 'company_phone_numbers' ];

	$fields['company']['phone_numbers'] = $fields_phone_numbers;

# Directors

	$fields_directors = [];

	$fields_directors[] = [ 'id' => 'company_director_name',
	                        'field_name' => 'name',
	                        'label' => [ 'text' => ___( "Name" ) ],
	                        'element' => [ 'type' => 'text', 'model' => 'edit_item.director.name', 'enter' => "changeItem('director')" ],
	                        'section' => 'company_directors' ];
	                        
	$fields_directors[] = [ 'id' => 'company_director_title',
	                        'field_name' => 'title',
	                        'label' => [ 'text' => ___( "Title" ) ],
	                        'element' => [ 'type' => 'text', 'model' => 'edit_item.director.title', 'enter' => "changeItem('director')" ],
	                        'section' => 'company_directors' ];
	                        
	$fields_directors[] = [ 'id' => 'company_director_email',
	                        'field_name' => 'email',
	                        'label' => [ 'text' => ___( "Email" ) ],
	                        'element' => [ 'type' => 'text', 'model' => 'edit_item.director.email', 'enter' => "changeItem('director')" ],
	                        'section' => 'company_directors' ];
	                        
	$fields_directors[] = [ 'id' => 'company_director_phone_mobile',
	                        'field_name' => 'phone_mobile',
	                        'label' => [ 'text' => ___( "Phone (mobile)" ) ],
	                        'element' => [ 'type' => 'text', 'model' => 'edit_item.director.phone_mobile', 'enter' => "changeItem('director')" ],
	                        'section' => 'company_directors' ];
	                        
	$fields_directors[] = [ 'id' => 'company_director_phone_office',
	                        'field_name' => 'phone_office',
	                        'label' => [ 'text' => ___( "Phone (office)" ) ],
	                        'element' => [ 'type' => 'text', 'model' => 'edit_item.director.phone_office', 'enter' => "changeItem('director')" ],
	                        'section' => 'company_directors' ];

	$fields['company']['directors'] = $fields_directors;

# Documents

	$fields_documents = [];

	$fields_documents[] = [ 'id' => 'company_document_name',
	                          'field_name' => 'name',
	                          'label' => [ 'text' => ___( "Document Name" ) ],
	                          'element' => [ 'type' => 'text', 'model' => 'document.name', 'enter' => "updateDocument(document)" ],
	                          'section' => 'company_documents' ];

	$fields['company']['documents'] = $fields_documents;

return $fields;
