<?php

$fields['lists'] = [];

# Category

	$fields_category = [];

	$fields_category[] = [ 'id' => 'category_name',
			              'field_name' => 'name',
			              'label' => [ 'text' => ___( "Name" ) ],
			              'element' => [ 'type' => 'text', 'model' => 'category.name', 'enter' => "updateCategory()" ],
			              'section' => 'list_categories' ];

	$fields['lists']['category'] = $fields_category;

# Lists

	$fields_lists = [];

	$fields_lists[] = [ 'id' => 'list_name',
		              'field_name' => 'name',
		              'label' => [ 'text' => ___( "List Name" ) ],
		              'element' => [ 'type' => 'text', 'model' => 'list.name', 'enter' => "updateList()" ],
		              'section' => 'lists' ];

	$fields['lists']['lists'] = $fields_lists;

# Fields

	$fields_fields = [];

	$fields_fields[] = [ 'id' => 'field_label',
	                    'field_name' => 'label',
	                    'label' => [ 'text' => ___( "Label" ) ],
	                    'element' => [ 'type' => 'text', 'model' => 'field.label', 'enter' => "updateField()" ],
	                    'section' => 'list_fields' ];

	$fields_fields[] = [ 'id' => 'field_type',
	                    'field_name' => 'type',
	                    'label' => [ 'text' => ___( "Type" ) ],
	                    'element' => [ 'type' => 'select', 'model' => 'field.type', 'enter' => "updateField()", 'attributes' => [ ':options' => 'fromJSON(\'' . base64_encode( json_encode( FormEngine::getListFieldTypes() ) ) . '\')' ] ],
	                    'section' => 'list_fields' ];

	$fields['lists']['fields'] = $fields_fields;

return $fields;