<?php

$search_targets = [];

/* 
	About Scores:
	Lower is better. The score is a figure between 1 and 1000, where 1000 is extremely low priority.

*/

#Branch
$search_target_branch = [];
$search_target_branch['entity_type'] = 'branch';
$search_target_branch['class'] = '\App\Modules\Core\Models\Branch';
$search_target_branch['fields'] = [ 'name' => 1,
									'code' => 5,
									'address' => 25,
									'manager' => 50 ];
$search_target_branch['template'] = [ 'heading' => '%$name%',
										'url' => '/settings/branches/%$id%/edit',
										'description' => 'Branch Code: %$code%'
									];

$search_targets['branch'] = $search_target_branch;

#Company document
$search_target_company_document = [];
$search_target_company_document['entity_type'] = 'company_document';
$search_target_company_document['class'] = '\App\Modules\Core\Models\CompanyDocument';
$search_target_company_document['fields'] = [ 'name' => 1,
												'file_name' => 1,
												'notes' => 10 ];
$search_target_company_document['template'] = [ 'heading' => '%$name%',
												'url' => '/settings/company/%$id%/edit-document',
												'description' => 'File name: %$file_name%; type: %$file_type%; size: %$file_size% bytes'
											];
$search_targets['company_document'] = $search_target_company_document;

#User
$search_target_user = [];
$search_target_user['entity_type'] = 'user';
$search_target_user['class'] = '\App\User';
$search_target_user['fields'] = [ 'first_name' => 1,
									'last_name' => 1,
									'email' => 1,
									'about' => 10 ];
$search_target_user['template'] = [ 'heading' => '%$first_name% %$middle_name% %$last_name%',
												'url' => '/settings/people/%$id%/edit',
												'description' => 'Email: %$email%'
											];
$search_targets['user'] = $search_target_user;

return $search_targets;