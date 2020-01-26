<?php

namespace App\Modules\Core\Models;

use App\Model;
use App\User;
use Config;
use Audit;


class Permission extends Model
{
  
  protected $table = 'permission';
  protected $fillable = [ 'user_id', 'permissions', 'override', 'exceptions' ];

  public static $audit_class = "permission";

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
  
  public static function mapPermissions( $args ) {

  	$roles = expand_array( $args['roles'] ?? [], [ 'active' ], '\App\Modules\Core\Models\Role' );
  	$permission_overrides = expand_array( $args['permission_overrides'] ?? [], [ 'branches', 'active' ], '\App\Modules\Core\Models\Permission' );
  	$permission_restrictions = expand_array( $args['permission_restrictions'] ?? [], [ 'branches', 'active' ], '\App\Modules\Core\Models\Permission' );
  	$user_id = $args['user_id'] ?? 0;
  	$company_id = $args['company_id'] ?? Config::get('company_id');
  	$permissions = [];
  	
  	if ( !$company_id ) return [ 'errors' => ___( 'Missing company ID.' ) ];
  	if ( !$user_id ) return [ 'errors' => ___( 'Missing user ID.' ) ];

    foreach ( $roles as $role ) {

  		$role = (object) $role;

      if ( $role->active ?? false ) {

    		$branches = isset( $role->branches ) ? $role->branches : [];

    		$role_permissions = RolePermission::with('permission')->where('role_id', $role->id)->get();

    		foreach ( $role_permissions as $role_permission ) {

    			$permission = $role_permission->permission;
          $permission->status = 1;
          $permission->branches = $branches;
    			$permissions[] = $permission;

    		}

      }

  	}

  	foreach ( $permission_overrides as $permission ) {

  		$permission = (object) $permission;
  		$permission->status = ( $permission->active ?? false ) ? 1 : 0;
      $permission->type = 'override';

			$permissions[] = $permission;

  	}

  	foreach ( $permission_restrictions as $permission ) {

  		$permission = (object) $permission;
  		$permission->status = ( $permission->active ?? false ) ? 0 : 1;
      $permission->type = 'restrict';

			$permissions[] = $permission;

  	}

  	$map_ids = [];

    foreach ( $permissions as $entry ) {

      $permission = self::find( $entry->id );
  		$status = $entry->status;

  		if ( $permission ) {

  			if ( $entry->branches ?? null ) {

  				$branches = $entry->branches ?? [];

        } else {

          $branches = Branch::where('company_id', $company_id)->get();
          $entry->all_branches = true;

  			}

  			foreach ( $branches as $branch ) {

          if ( isset( $branch->active ) || ( $entry->all_branches ?? null ) ) {

            if ( ( $entry->type ?? null ) == 'restrict' ) {

              $status = $branch->active ? 0 : 1;

              if ( ( $entry->all_branches ?? null ) && ( $entry->active ?? false ) ) {

                $status = 0;

              }

            } else {

              $status = $branch->active ? 1 : 0;

              if ( ( $entry->all_branches ?? null ) && ( $entry->active ?? false ) ) {

                $status = 1;

              }

            }

          } else {

            $status = $branch->status;

          }

  				$hash = sha1( json_encode( [ $company_id, $branch->id, $user_id, $permission->id ] ) );

  				$map = PermissionMap::where( 'hash', $hash )->first();

  				if ( !$map ) {

	  				$new_map = [];
            $new_map['hash'] = $hash;
	  				$new_map['company_id'] = $company_id;
	  				$new_map['branch_id'] = $branch->id;
	  				$new_map['user_id'] = $user_id;
	  				$new_map['permission_id'] = $permission->id;
	  				$new_map['status'] = $status;

	  				$map = PermissionMap::create( $new_map );

	  				$map_ids[] = $map->id;

	  			} else {

	  				$map->status = $status;
	  				$map->save();

	  				$map_ids[] = $map->id;

	  			}

  			}

  		}

  	}

  	if ( $map_ids ) {

  		PermissionMap::whereNotIn( 'id', $map_ids )->update( [ 'status' => 0 ] );

  	}

  	return [ 'success' => 1 ];

  }

  public static function getPermissions( $args ) {

  	$user_id = $args['user_id'] ?? 0;
  	$company_id = $args['company_id'] ?? Config::get('company_id');

  	$permissions = [];

  	if ( !$user_id ) return [ 'errors' => ___( 'Missing user ID.' ) ];

  	$permissions_list_overrides = json_decode( User::find( $user_id )->permission_overrides ?? '[]' );
  	$permissions_list_restrictions = json_decode( User::find( $user_id )->permission_restrictions ?? '[]' );

  	$permissions_list_overrides_keys = [];
  	$permissions_list_restrictions_keys = [];

  	foreach ( $permissions_overrides_list as $permission ) {

  		$permission = (object) $permission;

  		$permissions_list_overrides_keys[$permission->slug] = $permission;

  	}

  	foreach ( $permissions_restrictions_list as $permission ) {

  		$permission = (object) $permission;

  		$permissions_list_restrictions_keys[$permission->slug] = $permission;

  	}

  	$permissions = self::all();
  	$permission_overrides = [];
  	$permission_restrictions = [];

  	foreach ( $permissions as $permission ) {

  		if ( $permissions_list_overrides_keys[$permission->slug] ?? null ) {

  			$permission_override = $permissions_list_overrides_keys[$permission->slug];

  		} else {

  			$permission_override = $permission;

  		}

  		$permission_overrides[] = $permission_override;
  		
  		if ( $permissions_list_restrictions_keys[$permission->slug] ?? null ) {

  			$permission_restriction = $permissions_list_restrictions_keys[$permission->slug];

  		} else {

  			$permission_restriction = $permission;

  		}

  		$permission_restrictions[] = $permission_restriction;

  	}

  	return [ 'permission_overrides' => $permission_overrides, 'permission_restrictions' => $permission_restrictions ];
  	
  }

}
