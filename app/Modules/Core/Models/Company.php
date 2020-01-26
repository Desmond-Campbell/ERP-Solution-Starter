<?php

namespace App\Modules\Core\Models;

use App\Model;

use App\Modules\Core\Services\ListData;
use App\Modules\Core\Services\Validation;

use Audit, Search;

if ( !class_exists('App\Modules\Core\Models\Company')) {

class Company extends Model
{
  
  protected $table = 'company';
  protected $fillable = [ 'name', 'type', 'tax_id', 'licence_number' ];

  public static $audit_class = "company";

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

  public static function fetch( $id = null ) {

  	$companies = self::where( 'status', 1 );
  	
  	if ( $id ) $companies = $companies->where( 'id', $id );

  	$companies = $companies->get();

  	foreach ( $companies as $company ) {

      unset( $company->updated_at );
      unset( $company->status );

  		$company->phone_numbers = (array) try_json_decode( $company->phone_numbers ) ?? [];
      $company->directors = (array) try_json_decode( $company->directors ) ?? [];
      $company->addresses = (array) try_json_decode( $company->addresses ) ?? [];

      $company->business_type = ListData::getListItem( 'company_types', $company->type );

      $documents = CompanyDocument::where('company_id', $id)->where('status', '<', 2)->orderBy('created_at', 'desc')->get();

      foreach ( $documents as $document ) {

        $document->size_info = byteSize( $document->file_size, 1 );

      }

      $company->documents = $documents;

    }

    if ( $id ) return $companies[0] ?? (object) [];

    return $companies;

  }

  public static function validate( $section, $data, $company_id ) {

    return Validation::validate( $section, $data, [ 'company_id' => $company_id ] );

  }

  public static function saveDetails( $company_id, $data ) {

    $fields = [ 'name', 'type', 'tax_id', 'licence_number' ];

    $company = self::find( $company_id );

    if ( $company ) {

      foreach ( $fields as $field ) {

        $company->{$field} = $data[$field] ?? '';

      }

      $company->save();

    }

  }

  public static function saveField( $company_id, $data, $field ) {

    $company = self::find( $company_id );

    if ( $company ) {

      $column = str_replace( 'company_', '', $field );

      $company->{$column} = json_encode( $data );
      $company->save();

    }

  }

}
  
}