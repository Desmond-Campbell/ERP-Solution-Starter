<?php

namespace App\Modules\Core\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;

use Main, Search;

class SearchController extends Controller
{
  
  public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth' ]); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function index() {

    allow( 'search' );
    
    return view( 'Core::search.index' );

  }

}
