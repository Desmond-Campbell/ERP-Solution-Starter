<?php

// Core

	// Search

		// Web

			Route::get('/search', '\App\Modules\Core\Controllers\SearchController@index');

		// API

			Route::get('/api/search', '\App\Modules\Core\Controllers\Api\SearchController@search');
			Route::post('/api/search', '\App\Modules\Core\Controllers\Api\SearchController@search');
