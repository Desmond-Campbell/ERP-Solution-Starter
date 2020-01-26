<?php

namespace App\Modules\Core\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;

use Company, Main;

class CompanyController extends Controller
{
  
	public function __construct(Request $R, Route $route) {

    $this->middleware(['web', 'auth']); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function create( Request $R ) {

  	allow( 'create_company' );

    $name = trim( $R->input( 'name' ) );
  	$error = '';
  	$result = [];

  	if ( $name ) {

  		if ( Company::where( 'name', $name )->count( 'id' ) ) {

	  		$error = ___( 'A company with that name already exists.' );

  		} else {

  			$company = Company::create( [ 'name' => $name ] );

  			$result = [ 'id' => $company->id ];

  		}

  	} else {

  		$error = ___( 'Please enter a company name. Thanks.' );

  	}

  	if ( !$error ) {

  		$result['success'] = 1;

  	} else {

  		$result['success'] = 0;
  		$result['errors'] = $error;

  	}

  	return response()->json( $result );

  }

  public function close( Request $R ) {

    
    $id = $R->input( 'id' );

  	allow( 'close_company', $id );

    Company::where( 'id', $id )->update( [ 'status' => 2 ] );

  	return [ 'success' => 1 ];

  }

}
