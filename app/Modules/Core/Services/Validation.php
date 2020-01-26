<?php

namespace App\Modules\Core\Services;

use Validator;
use Config;

class Validation
{
  
  public static function validate( $section, $data, $args = [] ) {

  	$validation_rules = self::getRules( $args );
    $rules = $validation_rules['rules'];
    $messages = $validation_rules['messages'];

    $rules = $rules[$section] ?? [];
    $messages = $messages[$section] ?? [];

    ///////////////////////////////////

    $validator = Validator::make( $data, $rules, $messages );

    if ( $validator->fails() ) {

      $errors = self::explain( $validator->errors() );

      return [ 'valid' => false, 'errors' => $errors['messages'], 'targets' => $errors['targets'] ];

    } else {

      return [ 'valid' => true ];

    }

  }

  public static function explain( $error_list ) {

  	if ( $error_list ) $error_list = $error_list->toArray();

  	$messages = [];
  	$targets = [];

  	foreach ( $error_list as $field => $errors ) {

  		$target = $errors[0]['target'] ?? '';
  		$field = $target . "_$field";

  		$targets[$target] = true;

  		$messages[$field] = $messages[$field] ?? [];

  		foreach ( $errors as $error ) {

	  		$messages[$field][] = ( $error['text'] ?? $error ) ?? ___( "There's a problem with this field." );

	  	}

  	}

  	return [ 'messages' => $messages, 'targets' => (object) $targets ];

  }

  public static function getRules( $args = [] ) {

    $lines = Config::get( 'validations', [] );

    foreach ( $lines as $line ) {

      if ( $line[1] ?? null ) {

        $section = $line[0];
        $field = $line[1];
        $type = $line[2];
        $value = $line[3];

        $rules[$section] = $rules[$section] ?? [];
        $rules[$section][$field] = $rules[$section][$field] ?? '';

        if ( $type == 'rules' ) {

          $rules[$section][$field] = self::replaceTags( $value, $args );

        } else {

          $messages[$section] = $messages[$section] ?? [];
          $messages[$section]["$field.$type"] = [ 'target' => $section, 'text' => ___( self::replaceTags( $value, $args ) ) ];

        }

      }

    }

    $validations = [ 'messages' => $messages, 'rules' => $rules ];

    return $validations;

  }

  public static function replaceTags( $content, $args ) {

    foreach ( $args as $key => $value ) {

      $content = str_replace( "%$key%", $value, $content );

    }

    return $content;

  }

}
