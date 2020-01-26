<?php

use Illuminate\Support\Facades\Auth;
use \App\Modules\Core\Services\FormEngine;
use \App\Modules\Core\Models\PermissionMap;

define( 'NL', "\n" );

function ___( $text ) {
	
	return $text;

}

function arg( $A, $key, $default = null ) {

	$A = (array) $A;

	if ( !empty( $A[$key] ) ) return $A[$key];

	return $default;

}

function try_json_decode( $string ) {

	if ( is_array( $string ) ) return $string;

	$string = $string !== null && $string != '' ? json_decode( $string ) : [];
	
	if ( $string ) $object = $string;
	else $object = (object) [];

	return $object;

}

function get_url() {

	return base64_encode( $_SERVER['REQUEST_URI'] );

}

function get_setting( $id, $default ) {

	$setting = \App\Modules\Setting::where( 'id', $id )->orWhere( 'key', $id )->first();

	if ( $setting ) return $setting->value;

	return $default;
	
}

function compress( $list, $key ) {

	$array = [];

	if ( !is_array( (array) $list ) ) return $array;

	foreach ( $list as $k => $a ) {

		$a->$key = $k;
		$array[] = $a;

	}

	return $array;

}

function app_url( $link ) {

	return '/' . config( 'user.handle' ) . $link;

}

function initUser(){

	if ( !Auth::guest() ) {

		$user = \App\User::find( Auth::user()->id );

  	Config::set('user.id', $user->id );
  	Config::set('user.handle', $user->handle );
  	Config::set('user.name', $user->name );

  }

}

////// String helper functions

function substr_rev( $start, $length, $text ) {

	$text = strrev( $text );
	$text = substr( $text, $start, $length );
	$text = strrev( $text );

	return $text;

}

function list_slug( $slug ) {

	$slug = str_replace( [ ' ', '_list', '_' ], '', $slug );

	return $slug;

}

function clean_date( $date ) {

	return explode( "T", $date )[0];

}

function bool_value( $value ) {

	if ( !$value ) return '0';

	$green_values = [ 'yes', 'true', '1' ];
	$red_values = [ 'no', 'false', '0' ];

	$value = strtolower( $value );
	$value = str_replace( $green_values, '1', $value );
	$value = str_replace( $red_values, '0', $value );

	return $value;

}

function escape( $string, $type = '"' ) {

	if ( ( !$type && stristr( $string, "'" ) ) || $type == "'" ) return str_replace( "'", "\\'", $string );
	if ( ( !$type && stristr( $string, '"' ) ) || $type == '"' ) return str_replace( '"', '\\"', $string );

	return $string;

}

function money( $value ) {

	return number_format( $value, 2 );
	
}

function allow( $permission_slug, $company_id = null, $branch_id = null, $entity_id = null ) {

	PermissionMap::screen( compact( 'permission_slug', 'company_id', 'branch_id', 'entity_id' ) );

}

function ddd( $var ) {

	print '<pre>';
	print_r( $var );
	print '</pre>';

	die;
	
}

function byteSize($bytes, $ceil = false)
{
  if ($bytes >= 1073741824)
  {
      $bytes = number_format($bytes / 1073741824, 2);
      $bytes = $ceil ? ceil( $bytes ) : $bytes;
      $bytes .= 'G';
  }
  elseif ($bytes >= 1048576)
  {
      $bytes = number_format($bytes / 1048576, 2);
      $bytes = $ceil ? ceil( $bytes ) : $bytes;
      $bytes .= 'M';
  }
  elseif ($bytes >= 1024)
  {
      $bytes = number_format($bytes / 1024, 2);
      $bytes = $ceil ? ceil( $bytes ) : $bytes;
      $bytes .= 'K';
  }
  elseif ($bytes > 1)
  {
      $bytes = $bytes . 'b';
  }
  elseif ($bytes == 1)
  {
      $bytes = $bytes . 'b';
  }
  else
  {
      $bytes = '';
  }

  return $bytes;
}

function element( $properties ) {
	return elements( [ $properties ] );
}

function elements( $elements ) {

	return FormEngine::elements( $elements );

}

function paginate( $args ) {

	return FormEngine::paginate( $args );

}

function table( $args ) {

	return FormEngine::table( $args );

}

function activities( $args ) {

	return ___( 'Nothing found.' );

}

function sortByLabel($a, $b)
{
    $a = $a['label'];
    $b = $b['label'];

    if ($a == $b) return 0;
    return ($a < $b) ? -1 : 1;
}

function get_route_info ( $action ) {

	return explode( '@', str_replace( 'Controllers\\', '', $action ) );

}

function get_active_class( $controller ) {

	$controller_name = strtolower( str_replace( 'Controller', '', config( 'controller_name' ) ) );

	return $controller_name == $controller ? ' active' : ' .r-' . $controller . ' .a-' . $controller_name;

}

function get_user_id(){
	
	return Config::get('user')->id ?? 0;

}

function get_company_id(){
	
	return Config::get('company')->id ?? 0;

}

function branding_css() {

	$header_background_colour = Setting::get( 'text_header_background_colour', '', get_company_id() );
	$header_text_colour = Setting::get( 'text_header_text_colour', '', get_company_id() );
	$title_background_colour = Setting::get( 'text_title_background_colour', '', get_company_id() );
	$title_text_colour = Setting::get( 'text_title_text_colour', '', get_company_id() );
	$logo_size = trim( str_ireplace( 'px', '', Setting::get( 'number_logo_size', '', get_company_id() ) ) );
	$title_size = trim( str_ireplace( 'px', '', Setting::get( 'number_title_size', '', get_company_id() ) ) );
	$title_spacing = trim( str_ireplace( 'px', '', Setting::get( 'number_title_spacing', '', get_company_id() ) ) );

	$css = '';

	if ( $header_background_colour ) {

		$css .= '.main-nav-container { background: ' . $header_background_colour . ' !important; }' . NL;

	}

	if ( $header_text_colour ) {

		$css .= '.main-nav-container a { color: ' . $header_text_colour . ' !important; }' . NL;

	}

	if ( $title_background_colour ) {

		$css .= '.content-header { background: ' . $title_background_colour . ' !important; }' . NL;

	}

	if ( $title_text_colour ) {

		$css .= '.content-header .page-title { color: ' . $title_text_colour . ' !important; }' . NL;

	}

	if ( $logo_size ) {

		$css .= '.main-nav-container .header-logo { height: ' . $logo_size . 'px !important; }' . NL;

	}

	if ( $title_size ) {

		$css .= '.content-header .page-title { font-size: ' . $title_size . 'px !important; }' . NL;

	}

	if ( $title_spacing ) {

		$css .= '.content-header { 
			padding-top: ' . $title_spacing . 'px !important; 
			padding-bottom: ' . $title_spacing . 'px !important; 
		}' . NL;

	}

	$css .=	'.main-nav-container a.dropdown-item { color: #000 !important; }' . NL;

	return $css;

}

function branding_html() {

	$display_logo = Setting::get( 'flag_display_company_logo', true, get_company_id() );
  $display_name = Setting::get( 'flag_display_company_name', true, get_company_id() );
  $logo_path = Setting::get( 'logo_path', '', get_company_id() );

	$branding_html = '';
  
  if ( $display_logo ) {

    if ( $logo_path ) {

      $branding_html = '<img src="' . Config::get('settings.logo_public_dir') . $logo_path . '" class="header-logo" /> &nbsp; ';

    } else {

      $branding_html = '<i class="fa fa-building"></i> &nbsp; ';
    
    }

  }

  if ( $display_name ) {

	  $company_name_colour = Setting::get( 'text_company_name_colour', '#FFFFFF', get_company_id() );

    $branding_html .= '<span style="color:' . $company_name_colour . '">' . ( config( 'company' )->name ?? config( 'app.name' ) ) . '</span>';

  }

  return $branding_html;

}

function favicon_link() {

	$favicon_path = Setting::get( 'favicon_path', '', get_company_id() );

	if ( $favicon_path ) {

    return '<link rel="shortcut icon" href="' . Config::get('settings.favicon_public_dir') . $favicon_path . '" />';

  }

  return '';

}

function user_avatar() {

  $avatar = Config::get('user')->avatar_path ?? null;

  if ( $avatar ) return '<img src="' . Config::get('settings.avatar_public_dir') . $avatar . '" class="user-avatar" />';
  else return '<i class="fa fa-user-circle"></i>';

}

function get_branch_id() {


  $setting_key = "company_" . get_company_id() . "_user_" . get_user_id() . '_branch';
  $branch_id = Setting::get( $setting_key, null, null );
  $branch = null;

  if ( $branch_id ) {

    $branch = \App\Modules\Core\Models\Branch::find( $branch_id );

    if ( $branch ) return $branch_id;

  } 

  return 0;

}

function esc( $text, $char, $replace ) {

	return str_replace( $char, $replace, $text );

}

function menu( $menus, $key, $type = null, $options = [] ) {

	$markup = '';
	$menu = $menus[$key] ?? [];

	ksort( $menu );

	foreach ( array_values( $menu ) as $item ) {

		$visibility = $item['visibility'] ?? 'all';

		$exclude = 0;

		if ( $options['guest'] ?? null ) {

			if ( $visibility == 'auth' ) {

				$exclude = 1;

			}

		} elseif ( $options['auth'] ?? null ) {

			if ( $visibility == 'guest' ) {

				$exclude = 1;

			}

		}

		if ( !$exclude ) {

			if ( ( $item['type'] ?? null ) == 'divider' ) {

				$markup .= '<b-dropdown-divider></b-dropdown-divider>';

			} elseif ( $item['items'] ?? null ) {

	      $markup .= '<b-nav-item-dropdown text="' . $item['text'] . '">';
				$markup .= menu( [ 'submenu' => $item['items'] ], 'submenu', 'dropdown', $options );
	      $markup .= '</b-nav-item-dropdown>';

			} else {

				if ( $type == 'dropdown' || ( $item['type'] ?? null ) == 'dropdown-item' ) {

					$markup .= '<b-dropdown-item ';

				} else {

					$markup .= '<b-nav-item ';

				}

				$markup .= ' href="' . ( $item['url'] ?? '#' ) . '" ';

				if ( $item['hover'] ?? null ) $markup .= 'v-b-popover.hover.bottom="\'' . $item['hover'] . '\'" ';

				$markup .= ' >';

				if ( $item['text'] ?? null ) $markup .= $item['text'];

				if ( $type == 'dropdown' || ( $item['type'] ?? null ) == 'dropdown-item' ) {

					$markup .= '</b-dropdown-item>';

				} else {

					$markup .= '</b-nav-item>';

				}

			}

		}

	}

	return $markup;

}

function admin_links() {

	$markup = '';

	foreach ( Config::get( 'menu' ) as $key => $menus ) {

		$name = str_replace( 'links_', '', $key );
		
		if ( substr( $key, 0, 6 ) == 'links_' ) {

			$markup .= '<div v-show="vars.settings_tab == \'' . $name . '\'">';
			$markup .= ' <div class="admin-links-container">';
			$markup .= ' 	<ul>';

			foreach ( $menus as $menu ) {
			
				foreach ( ( $menu['items'] ?? [] ) as $item_key => $item ) {

					$url = $item['url'] ?? '#';

					$markup .= '<li class="clickable">';
					$markup .= '<span class="admin-link-item-icon"><a href="' . $url . '"><i class="' . ( $item['icon'] ?? 'fa fa-gear' )  . '"></i></a></span>';
					$markup .= '<span class="admin-link-item-title"><a href="' . $url . '">' . ( $item['title'] ?? ' &nbsp; ' )  . '</a></span>';

					if ( $item['description'] ?? null ) {
						
						$markup .= '<span class="admin-link-item-description"><a href="' . $url . '">' . $item['description']  . '</a></span>';

					}

					if ( $item['module'] ?? null ) {

						$markup .= '<span class="admin-link-item-module"><a href="' . $url . '">' . ___( 'Module' ) . ': ' . $item['module']  . '</a></span>';

					}

					$markup .= '</li>';

				}

			}

			$markup .= '		</ul>';
			$markup .= '	</div>';
			$markup .= '</div>';

		}

	}

	return $markup;

}

function admin_links_side() {

	$markup = '';

	foreach ( Config::get( 'menu' ) as $key => $menu ) {

		$menu = array_values( $menu )[0];

		$name = str_replace( 'links_', '', $key );

		if ( substr( $key, 0, 6 ) == 'links_' ) {

			$markup .= '<li><a href="#" class="nav-item-link" :class="{ \'active\' : vars.settings_tab == \'' . $name . '\' }" @click="setSettingsTab(' . "'$name'" . ')">
				  <i class="' . ( $menu['icon'] ?? 'fa fa-cog' ) . ' nav-item-icon"></i> &nbsp; ' . ( $menu['title'] ?? ' &nbsp; ' ) .
				'</a>';

		}

	}

	return $markup;

}

function expand_array( $array, $properties, $class ) {

	$model = new $class;
	$virgin_array = $model->where('id', '>', 0)->get()->toArray();
	$adjusted_array = $final_array = [];

	foreach ( $array as $item ) {

		$item = (array) $item;
		$adjusted_array["{$item['id']}"] = $item;

	}

	foreach ( $virgin_array as $record ) {

		$item = $adjusted_array[$record['id']] ?? null;

		foreach ( $properties as $property ) {

			$record[$property] = $item[$property] ?? null;

		}

		$final_array[] = $record;

	}

	return $final_array;

}

function reduce_array( $array, $properties ) {

	$final_array = [];

	foreach ( $array as $item ) {

		$item = (array) $item;

		$new_item = [ 'id' => $item['id'] ?? 0 ];

		foreach ( $properties as $property ) {

			$new_item[$property] = $item[$property] ?? null;

		}

		$final_array[] = $new_item;

	}

	return $final_array;

}

function copyRow( $object, $relations = [] ) {

	  $new = $object->replicate();
	  $new->push();

    $object->relations = [];

    foreach ( $relations as $relation ) {

    	$object->load( $relation );

    }

    foreach ( $object->relations as $relationName => $values ){

      $new->{$relationName}()->sync($values);

    }

}