<?php

namespace App\Modules\Core\Models;

use App\Model;

use Audit;


class Setting extends Model
{

	protected $table = 'setting';
	protected $fillable = ['key', 'value', 'category', 'company_id'];

  public static $audit_class = "setting";

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

  public static function set($key, $value, $category = '', $company_id = null)
    {
        $data = [ 'key' => $key, 'value' => $value, 'category' => $category, 'company_id' => $company_id ];

        return self::updateOrCreate(['key' => $key], $data);
    
    }

    public static function stepUp($key, $company_id)
    {
        return self::step($key, 1, $company_id);
    }

    public static function step($key, $value, $company_id)
    {
        $option = self::where('company_id', $company_id)->where('key', $key)->first();
        $value = 0;

        if ($option) {
            $value = intval($option->value);
        }

        return self::set($key, $value + 1);
    }
    
    public static function get_raw($key, $company_id = null)
    {
        $items = self::where('key', $key);

        if ( $company_id ) {
            
            $items = $items->where('company_id', $company_id);

        }

        return $items->first();
    }

    public static function get($key, $default = '', $company_id = null)
    {
        $option = self::get_raw($key, $company_id);

        if ($option) {
            return $option->value;
        } else {
            return $default;
        }
    }

    public static function kill($key, $company_id)
    {
        return self::where('company_id', $company_id)->where( 'key', $key )->delete();
    }

    public static function compact( $category, $company_id ) {

      // This method will get all the items in a category and create one object.

      $collection = [];

      if ( !is_array( $category ) ) {

	      $settings = self::where('company_id', $company_id)->where( 'category', $category)->get();

			} else {

				$settings = $category;

			}

			foreach ( $settings as $s ) {

				$collection[$s->key] = $s->value;

			}

			return (object) $collection;

    }

}
