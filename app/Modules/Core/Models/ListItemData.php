<?php

namespace App\Modules\Core\Models;

use App\Model;
use Audit;


class ListItemData extends Model
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
    protected $table = "list_item_data";
    protected $fillable = [ 'list_item_id', 'list_field_id', 'value_paragraph', 'value_text', 'value_integer', 'value_decimal', 'meta' ];

    public static $audit_class = "list_item_data";

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
        return $this->belongsTo('App\Modules\Core\Models\Lists');
    }

    public function list_item() {
        return $this->belongsTo('App\Modules\Core\Models\ListItem');
    }

    public function list_field() {
        return $this->belongsTo('App\Modules\Core\Models\ListField');
    }

    public static function fetch( $lists_id, $company_id, $args ) {

      $list = Lists::with('list_category')->find($lists_id);

      if ( !$list ) return;

      $list_fields = ListField::where('lists_id', $lists_id)->get();
      $columns = [];

      foreach ( $list_fields as $field ) {

        $columns[] = [ 'id' => str_slug( $field->label, '_' ), 'title' => $field->label ];

      }

      $list_items = ListItem::where('lists_id', $lists_id);

      if ( $args['paging'] ?? null ) {

        $limit = $args['paging']['limit'] ?? 15;
        $page = $args['paging']['page'] ?? 1;
        $sortIcons = $args['paging']['sortIcons'] ?? [];
        // $sortField = $args['paging']['sortField'] ?? 'name';
        // $sortOrder = $args['paging']['sortOrder'] ?? 'asc';

        $list_items = $list_items->paginate( $limit, ['*'], null, $page );
        $paging = [ 'page' => $list_items->currentPage(), 'total' => $list_items->total(), 'pages' => $list_items->lastPage(), 'limit' => $limit, 'sortIcons' =>  $sortIcons ];
        $list_items = $list_items->items();

      } else {

        $list_items = $list_items->get();

      }

      $results = [ 'vertical' => [], 'horizontal' => [] ];

      foreach ( $list_items as $list_item ) {

        $hash = md5( $list_item->id );

        $meta = [ 'lists_id' => $list_item->lists_id, 'list_item_id' => $list_item->id ];
        $results['horizontal'][$hash] = $results['horizontal'][$hash] ?? [];
        $results['vertical'][$hash] = $results['vertical'][$hash] ?? [ 'data' => [], 'meta' => $meta ];

        $data = self::with('list_field')->where('list_item_id', $list_item->id)->get();

        foreach ( $data as $item ) {

          $field_name = $item->list_field->label;
          $field_type = $item->list_field->type;

          $meta = [ 'lists_id' => $list_item->lists_id, 'list_item_id' => $list_item->id, 'id' => $item->id ];
          
          $results['horizontal'][$hash][str_slug( $field_name, '_' )] = self::getItemValue( $item, $field_type );
          $results['horizontal'][$hash]['meta'] = $meta;
          $results['vertical'][$hash]['data'][] = [ 'label' => $field_name, 'value' => self::getItemValue( $item, $field_type ) ];

        }

      }

      $info = [];
      $info['list_name'] = $list->name;
      $info['lists_id'] = $list->id;
      $info['category_name'] = $list->list_category->name;

      return [ 'list_items' => array_map( 'array_values', $results ), 'columns' => $columns, 'list_info' => $info ];

    }

    public static function fetchSingle( $lists_id, $id ) {

      $id = intval( $id );

      $fields = ListField::where( 'lists_id', $lists_id );

      if ( !$id ) {

        $fields = $fields->where('status', 1);

      }

      $fields = $fields->get();

      $data = [];

      foreach ( $fields as $field ) {

        $data[str_slug( $field->label, '_' )] = [ 'field_id' => $field->id, 'field_type' => $field->type, 'field_label' => $field->label, 'value' => null ];

      }

      $list_item = ListItem::find( $id );

      if ( $list_item ) {

        $list_item_data = self::with('list_field')->where( 'list_item_id', $id )->get();

        foreach ( $list_item_data as $item ) {

          $field_label = $item->list_field->label;
          $field_type = $item->list_field->type;
          $data[str_slug( $field_label, '_' )]['value'] = self::getItemValue( $item, $field_type );

        }

      }

      return $data;

    }

    public static function updateSingle( $lists_id, $id, $data ) {

      $record = [ 'value_text' => null, 'value_paragraph' => null, 'value_integer' => null, 'value_decimal' => null ];

      $id = intval( $id );

      if ( !$id ) {

        $id = ListItem::create( [ 'lists_id' => $lists_id ] )->id;

      }

      foreach ( $data as $key => $item_data ) {

        $row = $record;

        $row["value_" . $item_data['field_type']] = $item_data['value'];
        $row['list_item_id'] = $id;
        $row['list_field_id'] = $item_data['field_id'];

        $existing_row = self::where('list_item_id', $id)->where('list_field_id', $item_data['field_id'])->first();

        if ( $existing_row ) {

          $existing_row->update( $row );

        } else {

          self::create( $row );

        }

      }

    }

    public static function getItemValue( $item, $type ) {

      switch ( strtolower( $type ) ):

        case 'number':
          $value = $item->value_integer;
        break;

        case 'decimal':
          $value = $item->value_decimal;
        break;

        case 'paragraph':
          $value = $item->value_paragraph;
        break;

        default:
          $value = $item->value_text;
        break;

      endswitch;

      return $value;

    }

}
