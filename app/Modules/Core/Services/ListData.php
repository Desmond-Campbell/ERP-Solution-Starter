<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Lists;
use App\Modules\Core\Models\ListItem;

class ListData {

	public static function getList( $id ) {

		$lists = [];
		
		$lists['company_types'] = [];
		$lists['company_types'][] = [ 'value' => 'Individual', 'label' => ___( 'Individual' ) ];
		$lists['company_types'][] = [ 'value' => 'Sole Trader', 'label' => ___( 'Sole Trader' ) ];
		$lists['company_types'][] = [ 'value' => 'Partnership', 'label' => ___( 'Partnership' ) ];
		$lists['company_types'][] = [ 'value' => 'Company', 'label' => ___( 'Company' ) ];

		$lists['indicator_types'] = [];
		$lists['indicator_types'][] = [ 'value' => 'Percentage', 'label' => ___( 'Percentage' ) ];
		$lists['indicator_types'][] = [ 'value' => 'Currency', 'label' => ___( 'Currency' ) ];
		$lists['indicator_types'][] = [ 'value' => 'Number', 'label' => ___( 'Number' ) ];
		$lists['indicator_types'][] = [ 'value' => 'Text', 'label' => ___( 'Text' ) ];

		$lists['indicator_display_types'] = [];
		$lists['indicator_display_types'][] = [ 'value' => 'Basic Display', 'label' => ___( 'Basic Display' ) ];
		$lists['indicator_display_types'][] = [ 'value' => 'Pie Chart', 'label' => ___( 'Pie Chart' ) ];
		$lists['indicator_display_types'][] = [ 'value' => 'Line Chart', 'label' => ___( 'Line Chart' ) ];
		$lists['indicator_display_types'][] = [ 'value' => 'Bar Chart', 'label' => ___( 'Bar Chart' ) ];

		// Indicator Sources

			$sources = [];
			$sources_ = [];
			$sources_['Users'] = [ "Number of Users", ];
			$source_id = 1;

			foreach ( $sources_ as $section => $list ) {

				foreach ( $list as $item ) {

					$source_id++;

					$sources[] = [ 
													'slug' => str_slug( $item ), 
													'label' => $item,
													'value' => $source_id, 
													'section' => $section
												];

				}

			}

			$lists['indicator_data_sources'] = [ [ 'value' => 1, 'label' => ___( 'Manual Data' ) ] ];

			foreach ( $sources as $list ) {

				$lists['indicator_data_sources'][] = $list;

			}

		$categories = Lists::with( 'list_item' )->where( 'slug', 'indicator_categories' )->first();

		if ( $categories ) {
		
			if ( count( $categories->list_item ) ) {

				$lists['indicator_categories'] = $categories->list_item;

			}

		}

		return $lists[$id] ?? [];

	}

	public static function getListItem( $list, $id ) {

		foreach ( self::getList( $list ) as $key => $list ) {

			if ( $list['value'] == $id ) return $list['label'];

		}

		return null;

	}

}
