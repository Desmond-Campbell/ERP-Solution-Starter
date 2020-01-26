<?php

namespace App\Modules\Core\Services;

use App\Model;
use Config;

class Audit extends Model
{

 	/**
   * This contains an array of attributes that you do not want to be mass assignable
   * @var array
   */
  protected $guarded = ['id'];
  /**
   * This is the name of the database table for this Model
   * @var string
   */
  protected $table = "audit";

  protected $fillable = [ 'object_id', 'action', 'class', 'type', 'changes', 'parent_class', 'parent_id', 'user_id', 'company_id', 'branch_id' ];

  public static $classes = [ 
  		'company' => 'App\Modules\Core\Models\Company',
      'branch' => 'App\Modules\Core\Models\Branch',
  		'company_document' => 'App\Modules\Core\Models\CompanyDocument',
      'dashboard' => 'App\Modules\Core\Models\Dashboard',
      'indicator' => 'App\Modules\Core\Models\Indicator',
      'indicator_data' => 'App\Modules\Core\Models\IndicatorData',
      'list_item' => 'App\Modules\Core\Models\ListItem',
      'lists' => 'App\Modules\Core\Models\Lists',
      'permission' => 'App\Modules\Core\Models\Permission',
      'permission_map' => 'App\Modules\Core\Models\PermissionMap',
      'role' => 'App\Modules\Core\Models\Role',
      'role_permission' => 'App\Modules\Core\Models\RolePermission',
      'setting' => 'App\Modules\Core\Models\Setting',
      'user_branch' => 'App\Modules\Core\Models\UserBranch',
      'user_role' => 'App\Modules\Core\Models\UserRole',
      'user' => 'App\User',
  	];

  public static function getModelChanges( $object )
  {

  	$dirty = $object->getDirty();
    $changes = [];

    foreach ($dirty as $field => $newdata)
    {
      $olddata = $object->getOriginal($field);
      if ($olddata != $newdata)
      {
        $changes[$field] = [ $olddata, $newdata ];
      }
    }

    return $changes;
  }

  public static function log( $args )
  {

  	$object = $args['object'];
  	$type = $args['type'] ?? null;

  	if ( !$object->id ) {
      $type = 'create';
      $object->id = 0;
    } else {
      $type = $type ?? 'save';
    }

    if ( $args['parent_class'] ?? null ) {

    	$args['parent_id'] = $object->{ $args['parent_class'] . '_id'};

    }

	  if ( $type == 'save' ) {

	  	$changes = self::getModelChanges( $object );

	  } else {

	  	$changes = [];

	  }

	  $audit = [];
	  $audit['action'] = Config::get( 'route_info', '' );
    $audit['class'] = $args['class'] ?? '';
    $audit['type'] = $type;
    $audit['object_id'] = $object->id;
    $audit['user_id'] = get_user_id();
    $audit['changes'] = json_encode( $changes );
    $audit['parent_class'] = $args['parent_class'] ?? '';
    $audit['company_id'] = get_company_id();
    $audit['branch_id'] = get_branch_id();
    $audit['parent_id'] = (int) ($args['parent_id'] ?? '');

    self::create( $audit );

  }

}