<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class ListField extends Model
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
    protected $table = "list_field";
    protected $fillable = [ 'lists_id', 'label', 'type', 'description', 'status', 'meta' ];

    public static $audit_class = "list_category";

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
        return $this->belongsTo('App\Modules\Core\Lists');
    }

}
