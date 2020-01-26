<?php

namespace App\Modules\Core\Models;

use App\Model;

use Audit, Search;

class CompanyDocument extends Model
{
  
  protected $table = 'company_document';
  protected $fillable = [ 'company_id',  'name',  'file_name',  'file_size',  'file_type',  'file_path',  'notes', 'status' ];

  public static $audit_class = "company_document";

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

}
