<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Config;

class SearchIndex extends Model
{
	protected $table = 'search_index';
	protected $fillable = [ 'company_id', 'search_register_id', 'token', 'metaphone', 'score', 'entity_type', 'entity_created_at', 'entity_updated_at' ];

	public static function createIndex( $id ) {

		$entry = SearchRegister::find( $id );

		if ( !$entry ) return [ 'errors' => ___( 'Search register entry not found for indexing.' ) ];

		$search_def = Config::get('search.' . $entry->entity_type);

		if ( !$search_def ) return [ 'errors' => ___( 'Search config definition not found for' ) . " {$entry->entity_type}." ];

		$class = $search_def['class'];

		$model = new $class;

		$entity = $model->find( $entry->entity_id );

		if ( !$entity ) {

			$entry->delete();
			self::where( 'search_register_id', $id )->delete();

		} else {

			$properties = $entry->properties;
			$properties = $properties ? unserialize( $properties ) : [];

			$tokens = [];

			foreach ( $properties as $field => $property ) {

				$score = 1000 - floatval( $property['score'] );
				$words = explode( ' ', $property['content'] );

				foreach ( $words as $word ) {

					$word = strtolower( trim( $word ) );

					$tokens[$word] = $tokens[$word] ?? 0;
					$tokens[$word] += $score;

				}

			}

			self::where( 'search_register_id', $id )->delete();

			foreach ( $tokens as $token => $score ) {

				if ( trim( $token ) ) {

					$new_index = [ 'token' => $token,
									'metaphone' => metaphone( $token ), 
									'score' => $score, 
									'search_register_id' => $id, 
									'entity_type' => $entry->entity_type, 
									'entity_created_at' => $entity->created_at, 
									'entity_updated_at' => $entity->updated_at,
									'company_id' => $entity->company_id ?? null, 
								];

					self::create( $new_index );

				}

			}

			$entry->index_version = time();
			$entry->save();

		}

	}

}
