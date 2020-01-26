<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class SearchRegister extends Model
{
    
    protected $table = 'search_register';
    protected $fillable = [ 'company_id', 'entity_type', 'entity_id', 'content', 'properties' ];

    public static function registerEntity( $entity_id, $entity_type ) {

		$search_def = Config::get('search.' . $entity_type);

		if ( !$search_def ) return [ 'errors' => ___( 'Search config definition not found for' ) . " $entity_type." ];

		$fields = $search_def['fields'];
		$class = $search_def['class'];

		$model = new $class;
		$entity = $model->find( $entity_id );

		if ( $entity ) {

	    	$entry = self::where( 'entity_id', $entity_id )->where( 'entity_type', $entity_type );

	    	if ( $entity->company_id ?? null ) {

				$entry = $entry->where( 'company_id', $entity->company_id );

			} else {

				$entry = $entry->whereNull( 'company_id' );

			}

			$entry = $entry->first();

			if ( !$entry ) {

				$new_entry = [ 'entity_type' => $entity_type, 'entity_id' => $entity_id, 'company_id' => $entity->company_id ?? null ];

				$entry = self::create( $new_entry );

			}

			$content = '';
			$properties = [];

			foreach ( $fields as $field => $score ) {

				$content .= $entity->{$field} ?? '';
				$properties[$field] = [ 'score' => $score, 'content' => $entity->{$field} ?? '' ];

			}

			$entry->content = trim( $content );
			$entry->properties = serialize( $properties );
			$entry->index_version = 0;
			$entry->save();

			$instant_indexing = Config::get('settings.instant_search_indexing');

			if ( $instant_indexing ) {

		      	SearchIndex::createIndex( $entry->id );

		    }

		}

	}

}
