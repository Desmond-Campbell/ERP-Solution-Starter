<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class ListCategory extends Model
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
    protected $table = "list_category";
    protected $fillable = [ 'company_id', 'name', 'global', 'slug' ];

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
        return $this->hasMany('App\Modules\Core\Lists');
    }

}
