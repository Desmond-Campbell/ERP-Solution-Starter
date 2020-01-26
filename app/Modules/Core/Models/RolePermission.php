<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class RolePermission extends Model
{
  
  protected $table = 'role_permission';
  protected $fillable = [ 'company_id', 'role_id', 'permission_id', 'status' ];

  public static $audit_class = "role_permission";

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

  public function permission() {
  	return $this->belongsTo(Permission::class);
  }

  public static function reassign( $company_id, $role_id, $permissions ) {

  	$active_ids = [];

  	foreach ( $permissions as $permission ) {

			$status = $permission['active'] ?? false;
			$status = $status ? 1 : 0;

  		$record = self::where('company_id', $company_id)->where('role_id', $role_id)->where('permission_id', $permission['id'])->first();

			if ( $record ) {

				$record->status = $status;
				$record->save();

			} elseif( $status ) {

				$new_record = [];
				$new_record['company_id'] = $company_id;
				$new_record['role_id'] = $role_id;
				$new_record['permission_id'] = $permission['id'];
				$new_record['status'] = 1;

				$record = self::create( $new_record );

  		}

  		if ( $status ) $active_ids[] = $permission['id'];

  	}

  	if ( $active_ids ) {

	  	self::where('company_id', $company_id)->where('role_id', $role_id)->whereNotIn('permission_id', $active_ids)->update( ['status' => 0]);

	  }

  }

}
