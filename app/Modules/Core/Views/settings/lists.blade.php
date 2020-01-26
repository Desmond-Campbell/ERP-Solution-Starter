@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "List Manager" ) }}</span>
  <span class="push-right"><a href="javascript:void(0);" class="btn btn-warning" onclick="Lists.editList(-1)"><i class="fa fa-plus"></i> {{ ___('Create a List') }}</a> &nbsp; &nbsp; </span>
</h1>

@stop

@section('content')
<div class="container" id="vLists" v-cloak>

  <b-button-group>
    <b-button variant="info" :class="{ 'active' : section == 'manage' }" @click="section = 'manage'; category_filter = null; fetchLists()">{{___('Manage Lists')}}</b-button>
    <b-button variant="info" :class="{ 'active' : section == 'view' }" @click="section = 'view'; fetchCategories()">{{___('View List Data')}}</b-button>
  </b-button-group>

  <b-button-group v-show="section == 'view'">
    <b-button variant="secondary" :class="{ 'active' : data_view_mode == 'horizontal' }" @click="data_view_mode = 'horizontal'"><i class="fa fa-bars"></i></b-button>
    <b-button variant="secondary" :class="{ 'active' : data_view_mode == 'vertical' }" @click="data_view_mode = 'vertical'"><i class="fa fa-th-list"></i></b-button>
  </b-button-group>

  <b-button-group v-show="section == 'view'">
    <b-button variant="default" class="bg-light" :class="{ 'active' : data_layout_mode == 1 }" @click="data_layout_mode = 1"><i class="fa fa-genderless"></i></b-button> &nbsp; <b-button variant="default" class="bg-light" :class="{ 'active' : data_layout_mode == 2 }" @click="data_layout_mode = 2"><i class="fa fa-genderless"></i><i class="fa fa-genderless"></i></b-button> &nbsp; <b-button variant="default" class="bg-light" :class="{ 'active' : data_layout_mode == 3 }" @click="data_layout_mode = 3"><i class="fa fa-genderless"></i><i class="fa fa-genderless"></i><i class="fa fa-genderless"></i></b-button>
  </b-button-group>

  <br />

  <hr />

  <br />

  <div v-show="section == 'manage'">

    <div class="row">
    
      <div class="col-md-3">
        
        <h3 class="form-heading">{{ ___('Categories') }}</h3>

        <div class="form-group push-down">
          <button class="btn btn-success" @click="editCategory(-1)"><i class="fa fa-plus"></i> {{___('New Category')}}</button>
        </div>

        <div v-show="edit_category_mode" class="form-container">
              
          @php

          elements( Config::get( 'forms.lists.category' ) )

          @endphp

          <div class="form-group">

            <b-form-checkbox id="category_global"
                           v-model="category.global"
                           value="true"
                           >
              {{ ___( "Global: make this available for all companies" ) }}
            </b-form-checkbox>
            
          </div>

          <div class="form-group">
            <button class="btn btn-primary btn-sm" @click="updateCategory()"><i class="fa fa-check"></i> {{ ___( 'OK' ) }}</button>
            &nbsp;
            <button class="btn btn-warning btn-sm" @click="cancelEditCategory(); fetchCategories()"><i class="fa fa-undo"></i></button>
          </div>

        </div>

        <ul class="list-group push-down h-700 no-x-scroll">
          <li v-for="(category, index) in categories" class="list-group-item">
            
            <a href="javascript:void(0);" @click="category_filter = category.id; fetchLists()">
              @{{category.name}}
            </a>
            
            <div class="push-right">
              <b-dropdown id="ddown15" text="" class="">
                <b-dropdown-item @click="editCategory(index)">{{ ___('Edit') }}</b-dropdown-item>
                <b-dropdown-item @click="deleteCategory(index)">{{ ___('Delete') }}</b-dropdown-item>
              </b-dropdown>
            </div>

          </li>
        </ul>
      </div>

      <div v-show="!edit_mode" :class="{ 'col-md-0' : edit_mode, 'col-md-9' : !edit_mode}">

        @php

        paginate( [
                    'item_singular' => 'list',
                    'item_plural' => 'lists',
                    'search_method' => 'fetchLists',
                  ] ) 

        @endphp

        <div v-if="lists.length">
            
          <ul class="list-grid">
            <li v-for="(list, index) in lists" class="list-grid-item">
              <span class="item-name">@{{list.name}}
                <span class="item-count" v-if="list.list_items">(@{{list.list_items.length}})</span>
                <span class="item-count" v-if="!list.list_items">(0)</span>
              </span>
              <span class="item-controls">
                <a href="javascript:void(0);" class="btn btn-link" @click="editList(index)"><i class="fa fa-edit"></i></a>
                <a href="javascript:void(0);" class="btn btn-link" @click="deleteList(list.id)"><i class="fa fa-trash"></i></a>
                <a href="javascript:void(0);" class="btn btn-link" @click="emptyList(list.id)"><i class="fa fa-exclamation-triangle"></i></a>
              </span>
            </li>
          </ul>

        </div>

      </div>

      <div v-show="edit_mode" class="p-l-15" :class="{ 'col-md-0' : !edit_mode, 'col-md-8' : edit_mode}">

        <div class="form-container"> 

          <h3 class="form-heading" v-show="edit_id < 0">{{ ___( "New List" ) }}</h3>
          <h3 class="form-heading" v-show="edit_id > -1">{{ ___( "Edit List" ) }}</h3>

          @php

          elements( Config::get( 'forms.lists.lists' ) )

          @endphp

          <div class="form-group">

            <b-form-checkbox id="list_global"
                           v-model="list.global"
                           value="true"
                           >
              {{ ___( "Global: make this available for all companies" ) }}
            </b-form-checkbox>
            
          </div>

          <a href="javascript:void(0);" v-show="list.show_fields" @click="list.show_fields = false"><i class="fa fa-minus"></i> {{___('Hide Fields')}}</a>
          <a href="javascript:void(0);" v-show="!list.show_fields" @click="list.show_fields = true"><i class="fa fa-plus"></i> {{___('Show Fields')}}</a>

          <div class="push-down edit-item-container" v-show="list.show_fields">
            
            <button @click="editField(-1)"><i class="fa fa-plus-circle"></i> {{___('Add a field')}}</button>

            <div v-show="!edit_field_mode" class="push-down">

              <ul class="list-group">
                <li v-for="(field, index) in list.fields" class="list-group-item">
                  @{{field.label}} (@{{field.type}})<br />
                  <span class="text-links-small">
                    <a href="javascript:void(0);" @click="editField(index)" title="{{___('Edit')}}"><i class="fa fa-edit"></i></a> &nbsp; 
                    <a href="javascript:void(0);" @click="deleteField(index)" title="{{___('Delete')}}"><i class="fa fa-trash"></i></a> &nbsp; 
                    <a href="javascript:void(0);" @click="changeFieldStatus(field.id); field.status = 1;" v-show="!field.status" title="{{___('Enable')}}"><i class="fa fa-eye-slash"></i></a>
                    <a href="javascript:void(0);" @click="changeFieldStatus(field.id); field.status = 0;" v-show="field.status" title="{{___('Disable')}}"><i class="fa fa-eye"></i></a>
                  </span>
                </li>
              </ul>

            </div>

            <div v-show="edit_field_mode" class="push-down">

              <div v-show="list.id < 1">
                <i class="fa fa-info-circle"></i> {{___('Save the list first and then add fields.')}}
              </div>

              <div v-show="list.id > 0">

                @php

                elements( Config::get( 'forms.lists.fields' ) )

                @endphp

                <div class="form-group">
                  <button class="btn btn-primary btn-sm" @click="updateListField()"><i class="fa fa-check"></i> {{ ___( 'OK' ) }}</button>
                  &nbsp;
                  <button class="btn btn-warning btn-sm" @click="cancelEditField()"><i class="fa fa-undo"></i></button>
                </div>

              </div>

            </div>

          </div>

          <div class="form-group push-down">
            <button class="btn btn-success" @click="updateList()"><i class="fa fa-check"></i> {{ ___( 'Save' ) }}</button>
            &nbsp;
            <button class="btn btn-danger" @click="cancelEditList()"><i class="fa fa-undo"></i> {{ ___( 'Cancel' ) }}</button>
            &nbsp;
            <button class="btn btn-info" @click="cancelEditList()"><i class="fa fa-arrow-left"></i> {{ ___( 'Go Back' ) }}</button>
          </div>

        </div>

      </div>

    </div>

  </div>

  <div v-show="section == 'view'">

    <div class="row">

      <div class="col-md-2" :class="{ 'col-md-2' : data_layout_mode == 1, 'hidden' : data_layout_mode != 1 || edit_data_mode }">
        <h3 class="form-heading">{{ ___('Categories') }}</h3>

        <ul class="list-group options-list push-down h-700 no-x-scroll">
          <li v-for="(category, index) in categories" class="list-group-item clickable" @click="category_filter = category.id; fetchLists()" :class="{ 'active' : category.id == category_filter }">
            @{{category.name}}
          </li>
        </ul>
      </div>

      <div class="col-md-2" :class="{ 'col-md-2' : data_layout_mode == 1, 'col-md-3' : data_layout_mode == 2, 'hidden' : data_layout_mode == 3 || edit_data_mode }">
        <h3 class="form-heading">
          {{ ___('Lists') }}
          <strong v-show="data_layout_mode == 2"> &nbsp; (@{{list_info.category_name}})</strong>
        </h3>

        <ul class="list-group options-list push-down h-700 no-x-scroll">
          <li v-for="(list, index) in lists_filtered" class="list-group-item clickable" @click="fetchListData(list.id)" :class="{ 'active' : list.id == current_lists_id }">
            @{{list.name}}
          </li>
        </ul>
      </div>

      <div class="col-md-8" :class="{ 'col-md-8' : data_layout_mode == 1 || edit_data_mode, 'col-md-9' : data_layout_mode == 2, 'col-md-12' : data_layout_mode == 3 }">

        <h3 class="form-header" v-show="data_layout_mode == 3 && !edit_mode"><a href="javascript:void(0);" @click="data_layout_mode = 1">@{{list_info.category_name}}</a> :: <a href="javascript:void(0);" @click="data_layout_mode = 2">@{{list_info.list_name}}</a></h3>

        <h3 class="form-header" v-show="data_layout_mode == 3 && edit_mode">@{{list_info.category_name}} :: @{{list_info.list_name}}</h3>

        <a href="javascript:void(0);" style="display:block" @click="editListItemData(list_info.lists_id, null)" v-show="current_lists_id"><i class="fa fa-plus-circle"></i> {{___('Add an Item')}}</a>

        <div v-if="list_items.horizontal.length" class="push-down">

          <div v-show="data_view_mode == 'horizontal'">

            <div v-if="!list_items.horizontal.length">
              {{ ___('No entries to show.') }}
            </div>

            <div v-if="list_items.horizontal.length">

            @php

            table( [
                      'item_singular' => 'list_item',
                      'item_plural' => 'list_items.horizontal',
                      'columns' => 'data_columns',
                      'class' => 'table table-fixed-header head-variant-dark table-outlined',
                      'menu_options' => [ 
                        [ 'action' => 'editListItemData(list_item.meta.lists_id, list_item.meta.list_item_id)', 'text' => ___('Edit') ],
                        [ 'action' => 'deleteListItem(list_item.meta.lists_id, list_item.meta.list_item_id)', 'text' => ___('Delete') ],
                      ]
                    ] )

            @endphp

            </div>

          </div>

          <div v-show="data_view_mode == 'vertical'">

            <div v-if="!list_items.vertical.length">
              {{ ___('No entries to show.') }}
            </div>

            <div v-if="list_items.vertical.length">

              <table class="table" v-for="(list_item, item_index) in list_items.vertical" width="90%">

                <thead>
                  <th width="20%">{{___('Field')}}</th>
                  <th width="70%">{{___('Value')}}</th>
                  <th>
                    <b-dropdown id="ddown15" text="" class="">
                      <b-dropdown-item @click="editListItemData(list_item.meta.lists_id, list_item.meta.list_item_id)">{{ ___('Edit') }}</b-dropdown-item>
                      <b-dropdown-item @click="deleteListItemData(list_item.meta.lists_id, list_item.meta.list_item_id)">{{ ___('Delete') }}</b-dropdown-item>
                    </b-dropdown>
                  </th>
                </thead>

                <tbody>

                  <tr v-for="(list_item_data, item_data_index) in list_item.data">
                    <td width="20%">@{{ list_item_data.label }}</td>
                    <td width="70%">@{{ list_item_data.value }}</td>
                    <td>&nbsp;</td>
                  </tr>

                </tbody>

              </table>

            </div>

          </div>

        </div>

      </div>

      <div v-show="edit_data_mode" class="p-l-15" :class="{ 'col-md-0' : !edit_data_mode, 'col-md-4' : edit_data_mode }">

        <div v-if="!Object.size( list_item_data )">
        
          <h3 class="text-lighter">{{___('Weird Error')}}</h3>

          <p>{{ ___('No fields were added to this list, so you cannot enter any data.') }}</p>

          <div class="form-group">
            <button class="btn btn-warning" @click="cancelUpdateListItemData()"><i class="fa fa-undo"></i></button>
          </div>

        </div>

        <div v-if="Object.size( list_item_data )" class="form-container">

          <div class="form-group" v-for="field in list_item_data">

            <label>@{{field.field_label}}</label>

            <div v-if="field.field_type == 'text'">
              <input type="text" class="form-control" v-model="field.value" />
            </div>

          </div>

          <div class="form-group">
            <button class="btn btn-success" @click="updateListItemData()"><i class="fa fa-check"></i> {{___('Save')}}</button>
            <button class="btn btn-warning" @click="cancelUpdateListItemData()"><i class="fa fa-undo"></i> {{___('Cancel')}}</button>
          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')
  <script>
    include( [ 'Core/Lists' ] );
  </script>
@endsection