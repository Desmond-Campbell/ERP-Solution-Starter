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
use App\Modules\Core\Models\CompanyDocument;

use App\Modules\Core\Services\Main;

class SettingController extends Controller
{
  
  public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth' ]); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function index() {

    allow( 'view_settings' );
    
    return view( 'Core::settings.index' );

  }

  public function company() {

    allow( 'update_company' );
    
    return view( 'Core::settings.company' );

  }

  public function editCompanyDocument( Request $R ) {

    allow( 'update_company_document' );

    $document_id = $R->route('document_id');
    
    return view( 'Core::settings.company', compact( 'document_id' ) );

  }

  public function downloadCompanyDocument( Request $R ) {

    allow( 'download_company_document' );

    $document_id = $R->route('id');
    
    $document = CompanyDocument::find( $document_id );

    if ( !$document ) return;

    $full_path = base_path( Config::get( 'settings.document_storage_dir' ) ) . $document->file_path;

    $file_name = $document->file_name;

    $content = file_get_contents( $full_path );

    header('Content-type: ' . $document->file_type);
    header('Content-Disposition: attachment; filename="' . $file_name . '"');

    print $content;

    die;

  }

  public function branch() {

    allow( 'update_branch' );
    
    return view( 'Core::settings.branch' );

  }

  public function editBranch( Request $R ) {

    allow( 'update_branch' );

    $id = $R->route('id');
    
    return view( 'Core::settings.branch', compact( 'id' ) );

  }

  public function closeBranch( Request $R ) {

    $id = $R->route( 'id' );

    allow( 'close_branch', null, $id );
    
    if ( $id ) {

      $branch = Branch::find( $id );

      if ( $branch ) {

        $branch_name = $branch->name;
        $branch_id = $branch->id;

        return view( 'Core::settings.close-branch', compact ( 'branch_name', 'branch_id' ) );

      }

    }

    return redirect( '/settings/branches' );

  }

  public function switchBranch( Request $R ) {

    $id = $R->route( 'id' );

    if ( $id ) {

      $company_id = Config::get( 'company_id' );

      allow( 'access_branch', $company_id, $id );

      Branch::switchTo( $id );

    }

    return redirect('/settings/branches');

  }

  public function branding( Request $R ) {

    $company_id = Config::get( 'company_id' );
    
    allow( 'edit_branding', $company_id );

    return view( 'Core::settings.branding' );  

  }

  public function people( Request $R ) {

    $company_id = Config::get( 'company_id' );
    
    allow( 'view_people', $company_id );

    return view( 'Core::settings.people' );  

  }

  public function editPerson( Request $R ) {

    allow( 'update_person' );

    $id = $R->route('id');
    
    return view( 'Core::settings.people', compact( 'id' ) );

  }

  public function roles( Request $R ) {

    $company_id = Config::get( 'company_id' );
    
    allow( 'view_roles', $company_id );

    return view( 'Core::settings.roles' );  

  }

  public function lists( Request $R ) {

    $company_id = Config::get( 'company_id' );
    
    allow( 'view_lists', $company_id );

    return view( 'Core::settings.lists' );  

  }

}
