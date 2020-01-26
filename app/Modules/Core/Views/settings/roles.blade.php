@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "Roles &amp; Permissions" ) }}</span>
  <span class="push-right"><a href="javascript:void(0);" class="btn btn-warning" onclick="Roles.editRole(-1)"><i class="fa fa-plus"></i> {{ ___('Create a Role') }}</a> &nbsp; &nbsp; </span>
</h1>

@stop

@section('content')
<div class="container" id="vRoles" v-cloak>

  <div class="row">
  
    <div :class="{ 'col-md-6' : edit_mode, 'col-md-12' : !edit_mode}">

      @php

      paginate( [
                  'item_singular' => 'role',
                  'item_plural' => 'roles',
                  'search_method' => 'fetchRoles',
                ] ) 

      @endphp

      <div v-if="roles.length">
          
        @php

        table( [
                  'item_singular' => 'role',
                  'item_plural' => 'roles',
                  'menu_options' => [ 
                    [ 'action' => 'editRole(role.id)', 'text' => ___('Edit') ],
                    [ 'action' => 'deleteRole(role.id)', 'text' => ___('Delete') ],
                    [ 'action' => 'duplicateRole(role.id)', 'text' => ___('Duplicate') ],
                  ]
                ] )

        @endphp

      </div>

    </div>

    <div v-show="edit_mode" class="p-l-15" :class="{ 'col-md-0' : !edit_mode, 'col-md-6' : edit_mode}">

      <div class="form-container"> 

        <h3 class="form-heading" v-show="edit_id < 1">{{ ___( "New Role" ) }}</h3>
        <h3 class="form-heading" v-show="edit_id > 0">{{ ___( "Edit Role" ) }}</h3>

        @php

        elements( Config::get( 'forms.roles.role' ) )

        @endphp

        <div class="form-group">
          <button class="btn btn-success" @click="updateRole()"><i class="fa fa-check"></i> {{ ___( 'OK' ) }}</button>
          &nbsp;
          <button class="btn btn-danger" @click="cancelEditRole()"><i class="fa fa-undo"></i> {{ ___( 'Cancel' ) }}</button>
        </div>

        <div class="form-group">
          <b-form-checkbox id="all_permissions"
                           v-model="role.all_permissions"
                           value="1"
                           >
            {{ ___( "Select all permissions" ) }}
          </b-form-checkbox>
        </div>

        <div v-show="!role.all_permissions">

          <h3 class="form-heading">{{ ___('Permissions') }}</h3>

          <div class="select-list-container permision-list">

            <div class="form-group">
              <b-form-checkbox id="permission_filter_check"
                               v-model="params.permissionsFilter"
                               value="1"
                               >
                {{ ___( "Show selected permissions only" ) }}
              </b-form-checkbox>
            </div>

            <div class="form-group">

              <input type="text" class="form-control" v-model="params.permissionSearchQuery" placeholder="{{ ___( 'Search permissions...') }}" />

            </div>

            <ul class="main-list">

              <li v-for="p in searchPermissions" v-show="!params.permissionsFilter || ( params.permissionsFilter && checkPermission( role, p ) )" class="clickable main-item" :class="{ 'active' : checkPermission( role, p ) }" @click="togglePermission( role, p ); refresh()">

                <span class="item-name"><i class="fa fa-check text-success" v-show="checkPermission( role, p )"></i><i class="fa fa-times text-danger" v-show="!checkPermission( role, p )"></i> @{{ p.name }}</span>
                <span class="item-description">@{{ p.description }}</span>

              </li>

            </ul>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')
  <script type="text/javascript">
    include( [ 'Core/Roles' ] );
  </script>
@endsection
