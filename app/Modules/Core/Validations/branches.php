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

$rules[] = [ 'branches', 'name', 'rules', 'required|max:64' ];
$rules[] = [ 'branches', 'name', 'required', 'Please enter a name.' ];
$rules[] = [ 'branches', 'name', 'max', 'Branch name is too long. Max is 64.' ];

return $rules;
