<?php

namespace App\Modules\Core\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Auth;
use Config;

use Setting, Main, Company;

class CompanyController extends Controller
{
  
  public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth' ]); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function index() {

  	$companies = Company::fetch();

    allow( 'view_companies' );

    /* ToDo: check to see if user has access to each of these companies */

  	return view( 'Core::companies.index', compact( 'companies' ) );

  }

  public function new() {

  	return view( 'Core::companies.new' );

  }

  public function edit() {

    allow( 'update_companies' );

    return view( 'Core::companies.edit' );

  }

  public function close( Request $R ) {

    $id = $R->route( 'id' );

    allow( 'close_company', $id );
    
    if ( $id ) {

      $company = Company::find( $id );

      if ( $company ) {

        $company_name = $company->name;
        $company_id = $company->id;

        return view( 'Core::companies.close', compact ( 'company_name', 'company_id' ) );

      }

    }

    return redirect( url( '/companies' ) );

  }

  public function switch( Request $R ) {

    $id = $R->route( 'id' );

    if ( $id ) {

      $company = Company::find( $id );

      if ( $company ) {

        $setting_key = "company_user_" . Config::get( 'user' )->id;

        Setting::set( $setting_key, $id, 'global', null );

        return redirect( Main::getHomepage() );

      }

    }

    return redirect( url( '/companies' ) );

  }

}
