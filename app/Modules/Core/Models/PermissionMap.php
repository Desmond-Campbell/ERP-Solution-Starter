<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;
use Config;
use DB;

class PermissionMap extends Model
{
  
  protected $table = 'permission_map';
  protected $fillable = [ 'permission_id', 'user_id', 'company_id', 'branch_id', 'hash', 'status' ];

  public static $audit_class = "permission_map";

  protected static function boot()
  {
    parent::boot();
    static::saved(
      function( $object )
      {          
        Audit::log( [ 'object' => $object, 'class' => self::$audit_class ] );
        return true;
      }
    );
    static::deleted(
      function( $object )
      {
        Audit::log( [ 'object' => $object, 'class' => self::$audit_class, 'type' => 'delete' ] );
        return true;
      }
    );
  }

  public static function screen( $args ) {

  	extract( $args );

    request()->path();

    $branch_id = Config::get('branch_id');
    $company_id = Config::get('company_id');

    $user_id = Config::get('user_id');

    $permission = Permission::where('slug', $permission_slug)->first();

    $entry = null;

    $entry = self::where('company_id', $company_id)->where('permission_id', $permission->id ?? 0)->where('branch_id', $branch_id)->where('user_id', $user_id)->where('status', 1)->first();
    
    $captured = false;

    if ( !$entry )  {

      $info = [ 'permission' => $permission_slug, 'user_id' => $user_id, 'branch_id' => $branch_id, 'company_id' => $company_id, 'permission_id' => $permission->id ?? 0 ];

      if ( env( 'CAPTURE_PERMISSIONS') ) {

        // In development mode, permissions are captured and logged, to ensure that you have coverage of all checkpoints.

        $hash = md5( $permission_slug );
        $object = DB::table('_unused')->where( 'field1', $hash )->first();

        if ( !$object ) {

          DB::table('_unused')->insert( [ 
                'category' => 'permission_list', 
                'field1' => $hash, 
                'field2' => $permission_slug, 
                'created_at' => date( 'Y-m-d H:i:s' ),
                'updated_at' => date( 'Y-m-d H:i:s' )
                ] );

        } else {

          $captured = true; 

        }

      }

      if ( !Config::get('settings.disable_permission_checks') ) { 

        if ( !$captured ) {

          ob_clean();
          $error = ___( 'Permission denied' ) . ': ' . $permission_slug;

          if ( substr( request()->path(), 0, 4 ) == 'api/' ) {

            return response()->json( [ 'errors' => $error ] );

          } else {
            
            print $error;
          
          }

          die;

        }

      }

    }
  	
  }

}
