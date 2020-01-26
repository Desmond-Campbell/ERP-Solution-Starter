<?php

namespace App\Modules\Core\Services;

use Auth;
use Config;

use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Company;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\PermissionMap;

class Main
{
  
	public static function getHomepage() {

		return '/';

	}

	public static function init( $request, $next, $route, $R ) {

		if ( Auth::check() ) {
        
      $user = Auth::user();
      Config::set( 'user', $user );
      Config::set( 'user_id', $user->id );

      $route_info = get_route_info( $route->getActionName() );

      $user_id = $user->id;
      $setting_key = "company_user_{$user->id}";
      $company_id = Setting::get( $setting_key, null, null );
      $company = null;

      if ( $company_id ) {

        $company = Company::find( $company_id );

      }

      $path = str_replace( request()->getHost() . '/', '', request()->path() );

      if ( !$company 
            && substr( $path, 0, 9 ) != 'companies'
            && substr( $path, 0, 13 ) != 'api/companies'
          ) {

        return redirect( '/companies' );

      }

      if ( $company ) {

        Config::set( 'company', $company );
        Config::set( 'company_id', $company->id );

      }

      $setting_key = "company_$company_id" . "_user_" . Config::get('user')->id . '_branch';
      $branch_id = Setting::get( $setting_key, null, null );
      $branch = null;

      if ( $branch_id ) {

        $branch = Branch::find( $branch_id );

      }

      if ( $company ) {

        if ( !$branch
              && substr( $path, 0, 17 ) != 'settings/branches'
              && substr( $path, 0, 21 ) != 'api/settings/branches'
              && substr( $path, 0, 9 ) != 'companies'
              && substr( $path, 0, 13 ) != 'api/companies'
             ) {
          return redirect('/settings/branches');
        } else {

          if ( $branch ) {

            Config::set( 'branch', $branch );
            Config::set( 'branch_id', $branch->id );

          }

        }

      }

      $route_descriptor = strtolower( str_replace( 'Controller', '', $route_info[0] ) . '.' . $route_info[1] );
      $pageid = dechex( crc32( $route_descriptor ) );

      Config::set( 'controller_name', $route_info[0] );
      Config::set( 'action_name', $route_info[1] );
      Config::set( 'route_info', $route_descriptor );
      Config::set( 'pageid', $pageid );

      // $access = PermissionMap::screen( $route_descriptor );

      // if ( !$access ) {

        // dd("Access Denied");

      // }

    }

    return $next($request);

	}

}
