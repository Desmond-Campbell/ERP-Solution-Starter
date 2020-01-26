<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class Lists extends Model
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
    protected $table = "list";
    protected $fillable = [ 'company_id', 'name', 'slug', 'list_category_id', 'description', 'global' ];

    public static $audit_class = "lists";

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

    public function list_items() {
        return $this->hasMany('App\Modules\Core\Models\ListItem');
    }

    public function list_category() {
        return $this->belongsTo('App\Modules\Core\Models\ListCategory');
    }

    public function fields() {
        return $this->hasMany('App\Modules\Core\Models\ListField');
    }

    public static function fetch( $id = 0, $company_id, $args = null ) {

      $lists = self::with('list_items', 'fields')->where(
          function( $q ) use ( $company_id ) {
            $q->where('company_id', $company_id);
            $q->orWhere('global', 1);
          });

      if ( $args['category_filter'] ?? null ) {

        $lists = $lists->where('list_category_id', $args['category_filter']);
        
      }
      
      if ( $id ) $lists = $lists->where( 'id', $id );

      if ( !$id && $args['paging'] ) {

        $limit = $args['paging']['limit'] ?? 15;
        $page = $args['paging']['page'] ?? 1;
        $sortIcons = $args['paging']['sortIcons'] ?? [];
        $sortField = $args['paging']['sortField'] ?? 'name';
        $sortOrder = $args['paging']['sortOrder'] ?? 'asc';

        $lists = $lists->orderBy( $sortField, $sortOrder );

        $keywords = $args['paging']['keywords'] ?? [];

        if ( $keywords ) {

          $lists = $lists->where(function($q) use ($keywords) { 
            $q->where('name', 'like', '%' . $keywords . '%');
          });

        }

        $lists = $lists->paginate( $limit, ['*'], null, $page );
        $paging = [ 'page' => $lists->currentPage(), 'total' => $lists->total(), 'pages' => $lists->lastPage(), 'limit' => $limit, 'sortIcons' =>  $sortIcons ];
        $lists = $lists->items();

        foreach( $lists as $list ) {

          $list->show_fields = false;

        }

        return [ 'lists' => $lists, 'paging' => $paging ];

      } else {

        $lists = $lists->get();
      
      }

      if ( $id ) return $lists[0] ?? (object) [];

      return $lists;

    }

}
