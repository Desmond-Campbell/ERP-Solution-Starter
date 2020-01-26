<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\SearchRegister;
use App\Modules\Core\Models\SearchIndex;
use Config, DB;

class Search
{

 	public static function catch( $args )
  {

  	$object = $args['object'];
  	$class = $args['class'] ?? null;

  	if ( $class && ( $object->id ?? 0 ) ) {

      SearchRegister::registerEntity( $object->id, $class );

    }

  }

  public static function find( $company_id, $keywords, $args = [] ) {

    $words = explode( ' ', $keywords );

    $suggestions = '';
    $missed_words = 0;

    foreach ( $words as $word ) {

      $found = SearchIndex::where( function ( $q ) use ( $company_id ) { 
                                    $q->where('company_id', $company_id);
                                    $q->orWhereNull('company_id');
                                  }
                                  )->where('token', $word)->first();

      if ( !$found ) {

        $found = SearchIndex::where( function ( $q ) use ( $company_id ) { 
                                    $q->where('company_id', $company_id);
                                    $q->orWhereNull('company_id');
                                  }
                                  )->where('metaphone', metaphone( $word ) )->first();

        if ( $found ) {

          $words_corrected[] = $found->token; 
          $missed_words++;
        
        } else {

          $words_corrected[] = $word; 

        }

      } else {

        $words_corrected[] = $word;

      }

    }

    if ( $missed_words ) {

      $suggestions = ___( 'Did you mean' ) . ' <a href="/search?q=' . urlencode( implode( ' ', $words_corrected ) ) . '">' . implode( ' ', $words_corrected ) . '</a>?' ;

    }

    $records = SearchIndex::where( function ( $q ) use ( $company_id ) { 
                                    $q->where('company_id', $company_id);
                                    $q->orWhereNull('company_id');
                                  }
    )->where( function ( $q ) use ( $words ) {
      foreach ( $words as $word ) {
        $q->orWhere( 'token', 'like', "%$word%" );
      }
    })
    ->groupBy('search_register_id')
    ->select( DB::raw( 'sum(score) as weight, search_register_id' ) )
    ->orderBy('weight', 'desc');

    $limit = $args['paging']['limit'] ?? 15;
    $page = $args['paging']['page'] ?? 1;

    $records = $records->paginate( $limit, ['*'], null, $page );
    $paging = [ 'page' => $records->currentPage(), 'total' => $records->total(), 'pages' => $records->lastPage(), 'limit' => $limit ];
    $records = $records->items();

    return [ 'results' => self::transformRecords( $records ), 'paging' => $paging, 'suggestions' => $suggestions ];

  }

  public static function transformRecords( $records ) {

    $transformed_records = [];

    foreach ( $records as $record ) {

      $entry = SearchRegister::find( $record->search_register_id );

      if ( $entry ) {

        $search_def = Config::get('search.' . $entry->entity_type);

        if ( $search_def ) {

          $class = $search_def['class'];
          $template = $search_def['template'];

          $model = new $class;
          $entity = $model->find( $entry->entity_id );

          if ( $entity && $template ) {

            $heading = self::decodeFields( $entity, $template['heading'] ?? '' );
            $url = self::decodeFields( $entity, $template['url'] ?? '' );
            $description = self::decodeFields( $entity, $template['description'] ?? '' );

            $result_entry = [ 'heading' => $heading, 'url' => $url, 'description' => $description, 'type' => str_replace( '_', ' ', $entry->entity_type ) ];

            $transformed_records[] = $result_entry;

          }

        }

      }

    }

    return $transformed_records;

  }

  public static function decodeFields( $entity, $template ) {

    preg_match_all( '/%\$(.*)%/siU', $template, $M );

    for ( $m = 0; $m < count( $M[1] ); $m++ ) {

      $value = $entity->{$M[1][$m]} ?? '';
      $template = str_replace( $M[0][$m], $value, $template );

    }

    return $template;
  
  }

}