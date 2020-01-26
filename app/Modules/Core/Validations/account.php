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

$rules[] = [ 'account_personal', 'email', 'rules', 'required|unique:users,email,%user_id%,id' ];
$rules[] = [ 'account_personal', 'email', 'required', 'Please enter an email address.' ];
$rules[] = [ 'account_personal', 'name', 'unique', 'This email address is being used by someone else.' ];

return $rules;
