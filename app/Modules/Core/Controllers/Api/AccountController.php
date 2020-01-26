<?php

namespace App\Modules\Core\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Config;
use \App\User;
use Hash;

use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\CompanyDocument;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\UserBranch;
use App\Modules\Core\Models\UserRole;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\RolePermission;

use App\Modules\Core\Services\Main;
use App\Modules\Core\Services\Validation;

class AccountController extends Controller
{
  
	public function __construct(Request $R, Route $route) {

    $this->middleware([ 'web', 'auth']); 
    $this->middleware( function ( $request, $next ) use ( $R, $route ) {

      return Main::init( $request, $next, $route, $R );

    });

  }

  public function fetchAccount( Request $R ) {

    $company_id = Config::get( 'company' )->id;

    allow( 'view_account', $company_id );

    $user_id = get_user_id();

    $account = User::find( $user_id );

    return response()->json( $account );

  }

  public function updateAccount( Request $R ) {

    $company_id = Config::get( 'company' )->id;

    allow( 'update_account', $company_id );

    $error = '';
    $result = $form = [];

    $data = [ 
        'first_name' => $R->input( 'first_name' ), 
        'middle_name' => $R->input( 'middle_name' ), 
        'last_name' => $R->input( 'last_name' ), 
        'dob' => $R->input( 'dob' ), 
        'about' => $R->input( 'about' ), 
        'gender' => $R->input( 'gender' ), 
        'email' => $R->input( 'email' ),
      ];

    $user = User::find( Config::get( 'user' )->id );

    $validated = Validation::validate( 'user_personal', $data, [ 'company_id' => $company_id ] );

    if ( !( $validated['valid'] ?? false ) ) {

      $error = ___('Please check the fields marked red for errors.');
      $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

      return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

    } else { 

      if ( $user ) {
      
        foreach ( $data as $field => $value ) {

          $user->{$field} = $value;

        }

        $user->save();

        $result['success'] = 1;

      }

    }

    return response()->json( $result );

  }

  public function changePassword( Request $R ) {

    $company_id = Config::get( 'company' )->id;

    allow( 'change_password', $company_id );

    $error = '';
    $result = $form = [];

    $data = [ 
        'new_password' => $R->input( 'new_password' ), 
        'new_password_confirmation' => $R->input( 'new_password_confirmation' ), 
      ];

    $password = $R->input( 'password' );

    $user = User::find( Config::get( 'user' )->id );

    if ( !Hash::check( $password, $user->password ) ) {
      $error = ___('The password you entered is incorrect.');
      $error_details = [ 'messages' => [ 'user_account_current_password' => ___('Please enter your current password.') ], 'targets' => (object) [ 'user_account' => true ] ];

      return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );
    }

    $validated = Validation::validate( 'user_account', $data, [ 'company_id' => $company_id ] );

    if ( !( $validated['valid'] ?? false ) ) {

      $error = ___('We could not update your password. Please check the errors in the account section.');
      $error_details = [ 'messages' => $validated['errors'], 'targets' => $validated['targets'] ];

      return response()->json( [ 'errors' => $error, 'error_details' => $error_details ] );

    } else { 

      if ( $user ) {
      
        $user->password = bcrypt( $data['new_password'] );
        $user->save();

        $result['success'] = 1;

      }

    }

    return response()->json( $result );

  }

  public function uploadAvatar(Request $R) {

    $company_id = Config::get( 'company' )->id;

    allow( 'upload_avatar', $company_id );

    $file = $_FILES['file'];
    $ext = pathinfo( $file['name'] )['extension'];
    $root_path = base_path();
    $env_storage_path = '/' . Config::get('settings.avatar_storage_dir');
    $company_storage_path = '/' . $company_id;
    $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

    if ( !file_exists( $full_company_storage_path ) ) {

      mkdir( $full_company_storage_path, 0777, true );

    }
    
    $new_file_name = str_random( 28 );

    $public_path = $company_storage_path . '/' . $new_file_name . '.' . $ext;
    $full_path = $full_company_storage_path . '/' . $new_file_name . '.' . $ext;

    $user = User::find( get_user_id() );

    if ( $user ) {

      $user->avatar_path = $public_path;
      $user->save();

      move_uploaded_file( $file['tmp_name'], $full_path );

    }

  }

  public function removeAvatar() {

    $company_id = Config::get( 'company' )->id;

    allow( 'delete_avatar', $company_id );
 
    $root_path = base_path();
    $env_storage_path = '/' . Config::get('settings.avatar_storage_dir');
    $company_storage_path = '/' . $company_id;
    $full_company_storage_path = $root_path . $env_storage_path . $company_storage_path;

    $user = User::find( get_user_id() );

    if ( $user ) {

      $avatar_path = $user->avatar_path;

      $full_avatar_path = $full_company_storage_path . $avatar_path;

      $user->avatar_path = '';
      $user->save();

      if ( file_exists( $full_avatar_path ) ) {

        @unlink( $full_avatar_path );

      }

    }

  }

}
