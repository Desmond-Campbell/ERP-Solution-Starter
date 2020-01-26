<?php

namespace App\Modules\Core\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Config;
use \App\User;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\CompanyDocument;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\UserBranch;
use App\Modules\Core\Models\UserRole;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\RolePermission;
use App\Modules\Core\Models\Lists;
use App\Modules\Core\Models\ListCategory;
use App\Modules\Core\Models\ListField;
use App\Modules\Core\Models\ListItemData;
use App\Modules\Core\Models\ListItem;

use App\Modules\Core\Services\Main;
use App\Modules\Core\Services\Validation;

use Search;

class SearchController extends Controller
{
  
	public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth']); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function search( Request $R ) {

    $company_id = Config::get('company_id');
    $query = $R->input('q');

    $results = Search::find( $company_id, $query );

    return response()->json( $results );

  }

}
