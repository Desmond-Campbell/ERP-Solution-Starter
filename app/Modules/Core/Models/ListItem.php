<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class ListItem extends Model
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
    protected $table = "list_item";
    protected $fillable = [ 'lists_id' ];

    public static $audit_class = "list_item";

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

    public function lists() {
        return $this->belongsTo('App\Modules\Core\Model\Lists');
    }

    public function list_iem_data() {
        return $this->belongsTo('App\Modules\Core\Model\ListItemData');
    }

    public static function getValue( $id ) {

    	$listitem = self::find( $id );

    	if ( $listitem ) {

    		return $listitem->title;

    	}

    }

    public static function customField( $item ) {

    	$custom_fields = $item->custom_fields;

  		if ( $custom_fields ) {

  			$custom_fields = (array) json_decode( $custom_fields );
  			
  			foreach ( $custom_fields as $key => $value ) {

  				$item->$key = $value;

  			}

  		}

  		$item->custom_fields = (array) json_decode( $item->custom_fields );

  		return $item;

    }

    public static function customFields( $collection ) {

    	foreach ( $collection as $c ) {

    		$c = self::customField( $c );

    	}

    	return $collection;

    }

}
