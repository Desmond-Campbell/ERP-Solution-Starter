<?php

namespace App\Modules\Core\Models;

use App\Model;
use Config;
use \App\Modules\Core\Services\Main;

use Audit, Search;

class Branch extends Model
{
  
  protected $table = 'branch';
  protected $fillable = [ 'company_id', 'name', 'code', 'address', 'manager' ];

  public static $audit_class = "branch";

  protected static function boot()
  {
    parent::boot();
    static::saved(
      function( $object )
      {          
        Audit::log( [ 'object' => $object, 'class' => self::$audit_class ] );
        Search::catch( [ 'object' => $object, 'class' => self::$audit_class ] );
        return true;
      }
    );
    static::deleted(
      function( $object )
      {
        Audit::log( [ 'object' => $object, 'class' => self::$audit_class, 'type' => 'delete' ] );
        Search::catch( [ 'object' => $object, 'class' => self::$audit_class, 'type' => 'delete' ] );
        return true;
      }
    );
  }

  public static function fetch( $id = 0, $company_id, $args = null ) {

  	$branches = self::where( 'status', 1 )->where('company_id', $company_id);
  	
  	if ( $id ) $branches = $branches->where( 'id', $id );

    if ( !$id && $args['paging'] ) {

      $limit = $args['paging']['limit'] ?? 15;
      $page = $args['paging']['page'] ?? 1;
      $sortIcons = $args['paging']['sortIcons'] ?? [];
      $sortField = $args['paging']['sortField'] ?? 'name';
      $sortOrder = $args['paging']['sortOrder'] ?? 'asc';

      $branches = $branches->orderBy( $sortField, $sortOrder );

      $keywords = $args['paging']['keywords'] ?? [];

      if ( $keywords ) {

        $branches = $branches->where(function($q) use ($keywords) { 
          $q->where('name', 'like', '%' . $keywords . '%');
          $q->orWhere('address', 'like', '%' . $keywords . '%');
          $q->orWhere('code', 'like', '%' . $keywords . '%');
          $q->orWhere('manager', 'like', '%' . $keywords . '%');
        });

      }

      $branches = $branches->paginate( $limit, ['*'], null, $page );
      $paging = [ 'page' => $branches->currentPage(), 'total' => $branches->total(), 'pages' => $branches->lastPage(), 'limit' => $limit, 'sortIcons' =>  $sortIcons ];
      $branches = $branches->items();

      return [ 'branches' => $branches, 'paging' => $paging ];

    } else {

      $branches = $branches->get();
    
    }

    if ( $id ) return $branches[0] ?? (object) [];

    return $branches;

  }

  public static function switchTo( $id ) {

    $setting_key = "company_" . Config::get( 'company_id' ) . "_user_" . Config::get('user')->id . '_branch';

    Setting::set( $setting_key, $id );

    return redirect( Main::getHomepage() );
  
  }

}
