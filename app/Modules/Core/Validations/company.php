<?php

/*
Example:
Each line can represent a ruleset or a message. Usually the first line carries the rules.
$rules[] = [ 
							*0 form name or section
							*1 field, 
							*2 string 'rules' or the rule identifier (e.g. required, max), to denote what the line represents ruleset
							*3 actual rules if line represents a ruleset; otherwise validation message
						]
*/

$rules = [];

$rules[] = [ 'company_details', 'name', 'rules', 'required|unique:company,name,%company_id%,id|max:50' ];
$rules[] = [ 'company_details', 'name', 'required', 'Please enter a name for this company.' ];
$rules[] = [ 'company_details', 'name', 'max', 'Company name is too long. Max is 64 characters long.' ];
$rules[] = [ 'company_details', 'name', 'unique', 'Another company exists with this name.' ];

$rules[] = [ 'company_addresses', 'address', 'rules', 'required|max:128' ];
$rules[] = [ 'company_addresses', 'address', 'required', 'Please enter an address.' ];
$rules[] = [ 'company_addresses', 'address', 'max', 'Address is too long. Max is 128 characters.' ];

$rules[] = [ 'company_phone_numbers', 'number', 'rules', 'required|max:32' ];
$rules[] = [ 'company_phone_numbers', 'number', 'required', 'Please enter a phone number.' ];
$rules[] = [ 'company_phone_numbers', 'number', 'max', 'Phone number must be 32 characters or shorter than that.' ];

$rules[] = [ 'company_directors', 'name', 'rules', 'required|max:64' ];
$rules[] = [ 'company_directors', 'name', 'required', 'Please enter a name.' ];
$rules[] = [ 'company_directors', 'name', 'max', 'Director\'s name is too long. Max is 64 characters.' ];

$rules[] = [ 'company_documents', 'name', 'rules', 'required|max:64' ];
$rules[] = [ 'company_documents', 'name', 'required', 'Please enter a name.' ];
$rules[] = [ 'company_documents', 'name', 'max', 'Name of document can\'t be longer than 32 characters.' ];

return $rules;
