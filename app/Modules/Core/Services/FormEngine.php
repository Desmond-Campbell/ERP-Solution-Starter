<?php

namespace App\Modules\Core\Services;

use Illuminate\Database\Eloquent\Model;

class FormEngine extends Model
{
  
	public static function elements( $elements, $options = [] ) {

		$form = '';

		foreach ( $elements as $element ) {

			$html = self::element( $element );
			$html = str_replace( '%attributes%', '', $html );
			$form .= $html;

		}

		print $form;

	}

  public static function element( $element ) {

		$type = $element['element']['type'] ?? 'text';
		$random_name = str_random( 10 );
		$element_id = $element['id'] ?? 'FIELD_' . $random_name;
		$element_name = $element['name'] ?? '';

		$section = $element['section'] ?? '';

		if ( !$element_name && $section ) {

			$element_name = $section . '_' . ( $element['field_name'] ?? $random_name );

		}

		$label = '';

		if ( $element['label'] ?? null ) {

			$label = '<label class="' . ( $element['label']['class'] ?? '' ) . '">' . $element['label']['text'] . '</label>';

		}

		$html = '';
		$options = [];
		$class = $element['class'] ?? '';

		if ( in_array( $type, [ 'text', 'select', 'textarea' ] ) ) {

			$class .= ' form-control input-lg';

		}

		$attributes = [];

		if ( $element['element']['model'] ?? '' ) {

			$attributes['v-model'] = $element['element']['model'];
		
		}

		if ( $element['element']['attributes'] ?? [] ) {

			foreach ( $element['element']['attributes'] as $attribute => $value ) {

				$attributes[$attribute] = $value;

			}
		
		}

		if ( $element['element']['enter'] ?? '' ) {

			$attributes['@enter'] = $element['element']['enter'];
		
		}

		if ( $element_name ) {

			$attributes[':class'] = $attributes[':class'] ?? '';
			$attributes[':class'] .= "'has-errors' : errors.$element_name";

		}		

		if ( $attributes[':class'] ?? null ) {

			$attributes[':class'] = '{ ' . $attributes[':class'] . ' }';

		}

		switch ( $type ):

			case 'text':

				$html = $label . '<input type="text" id="' . $element_id . '" name="' . $element_name . '" class="' . $class . '" %attributes% />';

			break;

			case 'textarea':

				$rows = $element['rows'] ?? '';
				$cols = $element['cols'] ?? '';
				$html = $label . '<textarea id="' . $element_id . '" name="' . $element_name . '" rows="' . $rows . '" cols="' . $cols . '" class="' . $class . '" %attributes%></textarea>';

			break;

			case 'radio':

				$radio_counter = 0;

				if ( $element['options'] ?? [] ) {

					$items = $element['options'];
					$items = array_unique( $items, SORT_REGULAR );

					foreach ( $items as $item )
					{

						$radio_field_id = $element_id . '_' . $radio_counter;
						$options[] = [ 'id' => $radio_field_id, 'value' => $item['value'], 'label' => $item['label'] ];
						$radio_counter++;

					}
					
					usort( $items, 'sortByLabel' );

				}

				$html = $label;
				$markup = '';

				foreach( $options as $option ) {

					$markup .= '<b-form-radio id="' . $option['id'] . '" name="' . $element_id . '" :value="' . $option['value'] . '">' . $option['label'] . '</b-form-radio>';

				}

				$markup = '<b-form-radio-group %attributes%>
										' . $markup . '
                  </b-form-radio-group>';

				$html .= '<div class="radio-group">' . $markup . '</div>';

			break;

			case 'checkbox':

				$html = '<input type="checkbox" id="' . $element_id . '" name="' . $element_name . '" value="1" class="' . $class . '" %attributes% />' . $label;

			break;

			case 'select':

				$select_counter = 0;
				
				if ( $element['options'] ?? [] ) {

					$items = $element['options'];
					$items = array_unique( $items, SORT_REGULAR );

					foreach ( $items as $item )
					{

						$options[] = [ 'value' => $item['value'], 'label' => $item['label'] ];
						$select_counter++;

					}

					usort( $items, 'sortByLabel' );

				}

				$html = $label;

				$markup = '';

				foreach( $options as $option ) {

					$markup .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';

				}

				$html .= '<v-select id="' . $element_id . '" name="' . $element_name . '" class="' . $class . '" %attributes% >' . $markup . '</v-select>';

			break;

			case 'date':

				$html = $label . '<date-picker id="' . $element_id . '" name="' . $element_name . '" editable lang="en" %attributes%></date-picker>';

			break;

			default:

				$html = '';

			break;

		endswitch;

		$attributes_html = '';

		foreach ( $attributes as $key => $attribute ) {

			$attributes_html .= ' ' . $key . '="' . $attribute . '" ';

		}

		$html = str_replace( '%attributes%', $attributes_html, $html );

		$result = '<div class="form-group">';
		$result .= $html;
		$result .= '<div class="field-error" v-if="errors.' . $element_name . '">{{errors.' . $element_name . '[0]}}</div>';
		$result .= '</div>';

		return $result;

	}

	public static function paginate( $args ) {

		$search_method = $args['search_method'] ?? 'fetch';
		$item_singular = $args['item_singular'] ?? 'item';
		$item_plural = $args['item_plural'] ?? 'items';

		$pagination = '<b-row>
        <b-col><b-pagination base-url="#" :total-rows="paging.total" :per-page="paging.limit" v-model="paging.page" :simple="false" @change="pageChange"></b-pagination></b-col>
        <b-col class="p-110">
            <b-input-group>
              <b-form-input size="md" class="mr-md-2" type="text" v-model="paging.keywords" placeholder="' . ___('Search') . '" @enter="' . $search_method . '()"></b-form-input>
              <b-button size="md" class="my-2 my-md-0 btn-primary" @click="' . $search_method . '()">' . ___('Search') . '</b-button>
            </b-input-group>
        </b-col>
        <b-col class="p-10"><span class="push-right">{{ paging.total }} <span v-show="paging.total == 1">' . $item_singular . '</span><span v-show="paging.total != 1">' . $item_plural . '</span> | ' . ___('Showing') . ' {{ Math.min( paging.total, ( paging.page -1 ) * paging.limit + 1 ) }} - {{ Math.min( paging.total, ( paging.page -1 ) * paging.limit + paging.limit ) }} </span></b-col>
      </b-row>';

    print $pagination;

	}

	public static function table( $args ) {

		$item_singular = $args['item_singular'] ?? 'item';
		$item_plural = $args['item_plural'] ?? 'items';
		$class = $args['class'] ?? 'table table-fixed-header head-variant-dark table-outlined fixed-header-700 ';
		$var_columns = $args['columns'] ?? 'columns';
		$menu = '';
		$menu_column = $args['menu_column'] ?? true;
		
		$head = '<thead>
            
            <tr>
              <th v-for="(column, c) in ' . $var_columns . '" @click="sortBy(column.id)" class="clickable">
                {{ ___t( column.title ) }} <span v-html="paging.sortIcons[column.id]"></span>
              </th>';

    $body = '
          <tbody>

            <tr v-for="' . $item_singular . ' in ' . $item_plural . '">
              <td v-for="(column, c) in ' . $var_columns . '">
                {{ ' . $item_singular . '[column.id] }}
              </td>';

    if ( $menu_column ) {

			$head .= '
							<th>
                &nbsp;
              </th>';

      $menu_options = $args['menu_options'] ?? [];

      if ( $menu_options ) {

      	$options = '';

      	foreach ( $menu_options as $option ) {

      		$options .= '<b-dropdown-item @click="' . $option['action'] . '">' . $option['text'] . '</b-dropdown-item>';

      	}

      	$menu = '<b-dropdown id="ddown1" text="" class="" right>
                  
                  ' . $options . '

                </b-dropdown>';

      }

      $body .= '

              <td class="text-right">

                ' . $menu . '

              </td>';

    }

    $head .= '
    				</tr>

          </thead>';

    $body .= '
            </tr>

          </tbody>';

		$table = '<table class="' . $class . '">';
		$table .= $head;
		$table .= $body;
		$table .= '
        </table>';

    print $table;

	}

	public static function getListFieldTypes() {

		$types = [];
		$types[] = [ 'label' => ___( 'Text' ), 'value' => 'text' ];

		return $types;

	}

}
