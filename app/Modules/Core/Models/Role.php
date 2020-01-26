<?php

namespace App\Modules\Core\Models;

use App\Model;
use Config;
use Audit;


class Role extends Model
{
  
  protected $table = 'role';
  protected $fillable = [ 'name', 'description', 'company_id', 'role_id', 'permissions', 'all_permissions' ];

  public static $audit_class = "role";

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

  public function permissions() {
  	return $this->hasMany(RolePermission::class);
  }

  public static function fetch( $id = 0, $company_id, $args = null ) {

    $roles = self::with('permissions.permission')->where( 'status', 1 )->where( 'company_id', $company_id );
    
    if ( $id ) $roles = $roles->where( 'id', $id );

    if ( !$id && $args['paging'] ) {

      $limit = $args['paging']['limit'] ?? 15;
      $page = $args['paging']['page'] ?? 1;
      $sortIcons = $args['paging']['sortIcons'] ?? [];
      $sortField = $args['paging']['sortField'] ?? 'name';
      $sortOrder = $args['paging']['sortOrder'] ?? 'asc';

      $roles = $roles->orderBy( $sortField, $sortOrder );

      $keywords = $args['paging']['keywords'] ?? [];

      if ( $keywords ) {

        $roles = $roles->where(function($q) use ($keywords) { 
          $q->where('name', 'like', '%' . $keywords . '%');
        });

      }

      $roles = $roles->paginate( $limit, ['*'], null, $page );
      $paging = [ 'page' => $roles->currentPage(), 'total' => $roles->total(), 'pages' => $roles->lastPage(), 'limit' => $limit, 'sortIcons' =>  $sortIcons ];
      $roles = $roles->items();

      $result = [ 'roles' => $roles, 'paging' => $paging ];

    } else {

      $result = [ 'roles' => $roles->get() ];
    
    }

    foreach ( $result['roles'] as $role ) {

      if ( $role->all_permissions ) {

        $role->permissions_summary = ___( 'All permissions' );

      } else {

        $role->permissions_summary = '';
        $summary = [];
        $c = 0;
        $permission_count = count( $role->permissions );

        foreach ( $role->permissions as $p ) {

          if ( $p->status ) {

            $c++;

          }

          if ( $c <= 3 && $p->status ) {

            $summary[] = $p->permission->name;

          }

        }

        if ( $summary ) {

          $role->permissions_summary = implode( ', ', $summary );

          if ( $c > 3 ) {

            $role->permissions_summary .= ' ...';

          }

        } else {

          $role->permissions_summary = __('None');

        }

      }

    }

    if ( $id ) return $result[0]['roles'] ?? (object) [];

    return $result;

  }

	public static function prepare( $company_id, $id ) {

    $role = self::find( $id );

    $permissions = Permission::all();

    foreach ( $permissions as $permission ) {

    	$active = RolePermission::where('company_id', $company_id)->where('role_id', $id)->where('permission_id', $permission->id)->where('status', 1)->first();

    	if ( $active ) {

    		$permission->active = true;

    	}

    }

    $role->permissions = $permissions;

    return $role;

  }

}
