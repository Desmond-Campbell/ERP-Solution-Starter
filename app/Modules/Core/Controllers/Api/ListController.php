<?php

namespace App\Modules\Core\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Config;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\Branch;

use App\Modules\Core\Services\Main;
use App\Modules\Core\Services\ListData;

class ListController extends Controller
{
  
	public function __construct(Request $R, Route $route) {

    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function fetch( Request $R ) {

    $lists = $R->input( 'lists' );
    $data = [];

    if ( explode( ',', $lists ) ) {

      foreach ( explode( ',', $lists ) as $listid ) {

        $listid = trim( $listid );
        $list = ListData::getList( $listid );
        $data[$listid] = $list;

      }

    }

    return response()->json( $data );

  }

}
