<?php

// Core

	// Companies

		// Web

			Route::get('/companies', '\App\Modules\Core\Controllers\CompanyController@index');
			Route::get('/companies/new', '\App\Modules\Core\Controllers\CompanyController@new');
			Route::get('/companies/{id}', '\App\Modules\Core\Controllers\CompanyController@view');
			Route::get('/companies/{id}/switch', '\App\Modules\Core\Controllers\CompanyController@switch');
			Route::get('/companies/{id}/edit', '\App\Modules\Core\Controllers\CompanyController@edit');
			Route::get('/companies/{id}/close', '\App\Modules\Core\Controllers\CompanyController@close');
			
		// Documents

			Route::get('/public/documents/{id}/download', '\App\Modules\Core\Controllers\SettingController@downloadCompanyDocument');

		// API

			Route::post('/api/companies/create', '\App\Modules\Core\Controllers\Api\CompanyController@create');
			Route::post('/api/companies/{id}/update', '\App\Modules\Core\Controllers\Api\CompanyController@update');
			Route::post('/api/companies/close', '\App\Modules\Core\Controllers\Api\CompanyController@close');

	// Settings

		// Web

			Route::get('/settings', '\App\Modules\Core\Controllers\SettingController@index');
			
			// Company Info
			Route::get('/settings/company', '\App\Modules\Core\Controllers\SettingController@company');
			Route::get('/settings/company/{document_id}/edit-document', '\App\Modules\Core\Controllers\SettingController@editCompanyDocument');

			// Branch
			Route::get('/settings/branches', '\App\Modules\Core\Controllers\SettingController@branch');
			Route::get('/settings/branches/{id}/close', '\App\Modules\Core\Controllers\SettingController@closeBranch');
			Route::get('/settings/branches/{id}/switch', '\App\Modules\Core\Controllers\SettingController@switchBranch');
			Route::get('/settings/branches/{id}/edit', '\App\Modules\Core\Controllers\SettingController@editBranch');

			// People
			Route::get('/settings/people', '\App\Modules\Core\Controllers\SettingController@people');
			Route::get('/settings/people/{id}/edit', '\App\Modules\Core\Controllers\SettingController@editPerson');

			// Roles
			Route::get('/settings/roles', '\App\Modules\Core\Controllers\SettingController@roles');

			// Branding
			Route::get('/settings/branding', '\App\Modules\Core\Controllers\SettingController@branding');

			// List management
			Route::get('/settings/lists', '\App\Modules\Core\Controllers\SettingController@lists');
			
		// API

			// Company documents
			Route::post('/api/settings/company/upload-documents', '\App\Modules\Core\Controllers\Api\SettingController@uploadDocuments');
			Route::get('/api/settings/company/documents/fetch', '\App\Modules\Core\Controllers\Api\SettingController@getDocuments');
			Route::post('/api/settings/company/documents/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateDocument');
			Route::post('/api/settings/company/documents/{id}/archive', '\App\Modules\Core\Controllers\Api\SettingController@archiveDocument');
			Route::delete('/api/settings/company/documents/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteDocument');

			// Company Info
			Route::get('/api/settings/company/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchCompany');
			Route::post('/api/settings/company/{mode}', '\App\Modules\Core\Controllers\Api\SettingController@updateCompany');

			//Branch
			Route::get('/api/settings/branches/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchBranches');
			Route::post('/api/settings/branches/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchBranches');
			Route::get('/api/settings/branches/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchBranches');
			Route::post('/api/settings/branches/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateBranch');
			Route::post('/api/settings/branches/{id}/close', '\App\Modules\Core\Controllers\Api\SettingController@closeBranch');

			// People

			Route::get('/api/settings/people/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchPeople');
			Route::post('/api/settings/people/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchPeople');
			Route::get('/api/settings/people/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchPerson');
			Route::post('/api/settings/people/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updatePerson');
			Route::post('/api/settings/people/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deletePerson');

			// Roles

			Route::get('/api/settings/roles/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchRoles');
			Route::get('/api/settings/roles/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchRole');
			Route::post('/api/settings/roles/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateRole');
			Route::post('/api/settings/roles/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteRole');
			Route::post('/api/settings/roles/{id}/duplicate', '\App\Modules\Core\Controllers\Api\SettingController@duplicateRole');

			// Branding

			Route::get('/api/settings/branding/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchBranding');
			Route::get('/api/settings/branding/fetch-images', '\App\Modules\Core\Controllers\Api\SettingController@fetchBrandingImages');
			Route::post('/api/settings/branding/set-logo', '\App\Modules\Core\Controllers\Api\SettingController@setLogo');
			Route::post('/api/settings/branding/remove-logo', '\App\Modules\Core\Controllers\Api\SettingController@removeLogo');
			Route::post('/api/settings/branding/set-favicon', '\App\Modules\Core\Controllers\Api\SettingController@setFavicon');
			Route::post('/api/settings/branding/remove-favicon', '\App\Modules\Core\Controllers\Api\SettingController@removeFavicon');
			Route::post('/api/settings/branding/update', '\App\Modules\Core\Controllers\Api\SettingController@updateBranding');

			// List management
			Route::get('/api/settings/lists/categories/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchListCategories');
			Route::post('/api/settings/lists/categories/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateListCategory');
			Route::delete('/api/settings/lists/categories/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteListCategory');
			Route::post('/api/settings/lists/fields/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchListField');
			Route::post('/api/settings/lists/{lists_id}/fields/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateListField');
			Route::delete('/api/settings/lists/fields/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteListField');
			Route::post('/api/settings/lists/fields/{id}/update-status', '\App\Modules\Core\Controllers\Api\SettingController@updateListFieldStatus');
			Route::get('/api/settings/lists/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchLists');
			Route::post('/api/settings/lists/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchLists');
			Route::get('/api/settings/lists/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchLists');
			Route::post('/api/settings/lists/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateList');
			Route::delete('/api/settings/lists/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteList');
			Route::delete('/api/settings/lists/{id}/empty', '\App\Modules\Core\Controllers\Api\SettingController@emptyList');
			Route::get('/api/settings/lists/{id}/fetch-data', '\App\Modules\Core\Controllers\Api\SettingController@fetchListData');
			Route::post('/api/settings/lists/{id}/fetch-data', '\App\Modules\Core\Controllers\Api\SettingController@fetchListData');
			Route::get('/api/settings/lists/{lists_id}/item-data/{id}/fetch', '\App\Modules\Core\Controllers\Api\SettingController@fetchListItemData');
			Route::post('/api/settings/lists/{lists_id}/item-data/{id}/update', '\App\Modules\Core\Controllers\Api\SettingController@updateListItemData');
			Route::delete('/api/settings/lists/item-data/{id}/delete', '\App\Modules\Core\Controllers\Api\SettingController@deleteListItemData');
			
	// Account

		// Web

			Route::get('/account', '\App\Modules\Core\Controllers\AccountController@account');

		// API

			Route::get('/api/account/fetch', '\App\Modules\Core\Controllers\Api\AccountController@fetchAccount');
			Route::post('/api/account/update', '\App\Modules\Core\Controllers\Api\AccountController@updateAccount');
			Route::post('/api/account/password/change', '\App\Modules\Core\Controllers\Api\AccountController@changePassword');
			Route::post('/api/account/avatar/upload', '\App\Modules\Core\Controllers\Api\AccountController@uploadAvatar');
			Route::post('/api/account/avatar/remove', '\App\Modules\Core\Controllers\Api\AccountController@removeAvatar');

	// General

		// API

			Route::get('/api/lists/fetch', '\App\Modules\Core\Controllers\Api\ListController@fetch');
