<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class UserRole extends Model
{
  protected $table = 'user_role';
  protected $fillable = [ 'company_id', 'role_id', 'user_id', 'status' ];

  public static $audit_class = "user_role";

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

  public static function mergeRoles( $user_id, $company_id ) {

  	$roles = Role::where('company_id', $company_id)->get();

  	foreach ( $roles as $role ) {

  		if ( self::where('company_id', $role->company_id)->where('user_id', $user_id)->where('role_id', $role->id)->where('status', 1)->count() > 0 ) {

  			$role->active = true;

  		} else {

        $role->active = false;
        
      }

  	}

  	return $roles;

  }

  public static function mapRoles( $user_id, $roles, $company_id ) {

  	$selected_ids = [];

  	foreach ( $roles as $role ) {

  		$role_record = self::where('company_id', $role['company_id'])->where('user_id', $user_id)->where('role_id', $role['id'])->first();
  		$status = ( $role['active'] ?? false ) ? 1 : 0;

  		if ( $role_record ) {

  			$role_record->status = $status;
  			$role_record->save();

  		} else {

  			$role_record = self::create( [ 'company_id' => $company_id, 'user_id' => $user_id, 'role_id' => $role['id'], 'status' => $status ] );

  		}

  		if ( $status ) {

  			$selected_ids[] = $role_record->id;

  		}

  	}

  	self::whereNotIn('id', $selected_ids)->delete();

  }
}
