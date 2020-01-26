<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class UserBranch extends Model
{
  protected $table = 'user_branch';
  protected $fillable = [ 'company_id', 'branch_id', 'user_id', 'status' ];

  public static $audit_class = "user_branch";

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

  public static function mergeBranches( $user_id, $company_id ) {

  	$branches = Branch::where('company_id', $company_id)->get();

  	foreach ( $branches as $branch ) {

  		if ( self::where('company_id', $branch->company_id)->where('user_id', $user_id)->where('branch_id', $branch->id)->where('status', 1)->count() > 0 ) {

  			$branch->active = true;

  		} else {

        $branch->active = false;
        
      }

  	}

  	return $branches;

  }

  public static function mapBranches( $user_id, $branches, $company_id ) {

  	$selected_ids = [];

  	foreach ( $branches as $branch ) {

  		$branch_record = self::where('company_id', $branch['company_id'])->where('user_id', $user_id)->where('branch_id', $branch['id'])->first();
  		$status = ( $branch['active'] ?? false ) ? 1 : 0;

  		if ( $branch_record ) {

  			$branch_record->status = $status;
  			$branch_record->save();

  		} else {

  			$branch_record = self::create( [ 'company_id' => $company_id, 'user_id' => $user_id, 'branch_id' => $branch['id'], 'status' => $status ] );

  		}

  		if ( $status ) {

  			$selected_ids[] = $branch_record->id;

  		}

  	}

  	self::whereNotIn('id', $selected_ids)->delete();

  }
    
}
