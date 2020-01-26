<?php

namespace App\Modules\Core\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Auth;
use Config;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Role;

use App\Modules\Core\Services\Main;

class AccountController extends Controller
{
  
  public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth' ]); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function account() {

    allow( 'update_account' );
    
    return view( 'Core::account.index' );

  }
 
}
