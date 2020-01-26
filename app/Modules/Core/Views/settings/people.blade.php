@extends('layouts.app')

@section('content-header')

<h1 class="page-title">
  <span class="lead-text">{{ ___( "Settings" ) }}</span> &middot; <span class="secondary-text">{{ ___( "People" ) }}</span>
  <span class="push-right"><a href="javascript:void(0);" class="btn btn-warning" onclick="People.editPerson(-1)"><i class="fa fa-plus"></i> {{ ___('Add Someone') }}</a> &nbsp; &nbsp; </span>
</h1>

@stop

@section('content')
<div class="container" id="vPeople" v-cloak>

  <div class="row">

    <div class="col-md-9">
  
      <div v-show="mode != 'edit'">

        @php

        paginate( [
                    'item_singular' => 'person',
                    'item_plural' => 'people',
                    'search_method' => 'fetchPeople',
                  ] ) 

        @endphp

        <div v-if="people.length">
          
          @php

          table( [
                    'item_singular' => 'person',
                    'item_plural' => 'people',
                    'menu_options' => [ 
                      [ 'action' => 'editPerson(person.id)', 'text' => ___('Edit') ],
                      [ 'action' => 'deletePerson(person.id)', 'text' => ___('Delete') ],
                    ]
                  ] )

          @endphp

        </div>

      </div>

      <div v-show="mode == 'edit'">

        <div class="row">

          <div class="col-md-12">

            <div class="alert alert-info bg-white">
              <i class="fa fa-lock-open"></i> {{__('Edit Mode')}} &nbsp; &nbsp; <button class="btn btn-info btn-sm" @click="mode = 'view'; fetchPeople()"><i class="fa fa-undo"></i></button> &nbsp; &nbsp; <button class="btn btn-success btn-sm" @click="updatePerson()" v-show="dirty">{{ ___( 'Save' ) }}</button>
            </div>

            <div class="row push-down">

              <div class="col-md-3">

                <div class="side-menu">
                  <nav class="nav flex-column">
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'personal' || vars.person_edit_tab == '' }" @click="setPersonEditTab('personal')">
                      <i class="fa fa-bars nav-item-icon"></i>
                        {{ ___( 'Personal Info' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'roles' }" @click="setPersonEditTab('roles')">
                      <i class="fa fa-hand-point-right nav-item-icon"></i>
                        {{ ___( 'Roles' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'overrides' }" @click="setPersonEditTab('overrides')">
                      <i class="fa fa-lock-open nav-item-icon"></i>
                      {{ ___( 'Overrides' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'restrictions' }" @click="setPersonEditTab('restrictions')">
                      <i class="fa fa-minus-circle nav-item-icon"></i>
                      {{ ___( 'Restrictions' ) }}
                    </a>   
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'branches' }" @click="setPersonEditTab('branches')">
                      <i class="fa fa-building nav-item-icon"></i>
                        {{ ___( 'Branches' ) }}
                    </a>
                    <a href="javascript:void(0);" class="nav-item-link" :class="{ 'active' : vars.person_edit_tab == 'password' }" @click="setPersonEditTab('password')">
                      <i class="fa fa-key nav-item-icon"></i>
                        {{ ___( 'Password' ) }}
                    </a>
                  </nav>   
                </div>

              </div>

              <div class="col-md-9">

                <div class="tab-content">
                  
                  <div v-show="vars.person_edit_tab == 'personal' || vars.person_edit_tab == ''">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Personal Info')}}</h3>

                      @php

                      elements( Config::get( 'forms.people.personal' ) )

                      @endphp

                      <div class="form-group">
                        <button class="btn btn-primary" @click="updatePerson()"><i class="fa fa-check"></i> {{ ___( "OK" ) }}</button>
                        &nbsp;
                        <button class="btn btn-default" @click="mode = 'view'; fetchPeople()"><i class="fa fa-undo"></i> {{ ___( "Cancel" ) }}</button>
                      </div>

                    </div>

                  </div>

                  <div v-show="vars.person_edit_tab == 'roles'">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{ ___( 'Roles' ) }}
                        <strong><i v-b-popover.hover="'{{ ___( 'You can set up someone with a role and they will inherit all the permissions in that role. You can also exclude certain particular permissions from that role, or add some that are not in the role. That way you can have maximum flexibility. You have 2 options when it comes to specifying what roles can be used at what branches. You may either create a role with permissions that are branch specific, or you can create a role without branch specifics and set the branch specifications right here when you are assigning the role to a person.' ) }}'" title="{{ ___( 'Roles Setup' ) }}" class="fa fa-info-circle text-warning popup-icon"></i></strong>
                      </h3>

                      <div class="select-list-container push-down">

                        <div class="form-group">

                          <b-form-checkbox id="role_filter_check"
                                         v-model="params.rolesFilter"
                                         value="1"
                                         >
                            {{ ___( "Show selected roles only" ) }}
                          </b-form-checkbox>
                          
                        </div>

                        <div class="form-group">

                          <input type="text" class="form-control" v-model="params.roleSearchQuery" placeholder="{{ ___( 'Search roles...') }}" />

                        </div>

                        <ul class="main-list">

                          <li v-for="r in searchRoles" v-show="!params.rolesFilter || ( params.rolesFilter && checkRole( person, r ) )" class="clickable main-item" :class="{ 'active' : checkRole( person, r ), 'hidden' : !(!params.rolesFilter || ( params.rolesFilter && checkRole( person, r ) )) }" @click="toggleRole( person, r ); refresh()">

                            <span class="item-name"><i class="fa fa-check text-success" v-show="checkRole( person, r )"></i><i class="fa fa-times text-danger" v-show="!checkRole( person, r )"></i> &nbsp; @{{ r.name }}</span>
                            <span class="item-description">@{{ r.description }}</span>
                            
                          </li>

                        </ul>

                      </div>

                    </div>

                  </div>

                  <div v-show="vars.person_edit_tab == 'overrides'">
                  
                    <div class="form-container">

                      <h3 class="form-heading">{{ ___( 'Permission Overrides' ) }}</h3>

                      <div class="select-list-container push-down">

                        <div>

                          <div v-show="!editModeOverrideBranches">
                          
                            <div class="form-group">

                              <b-form-checkbox id="overrides_filter_check"
                                             v-model="params.overrideFilter"
                                             value="1"
                                             >
                                {{ ___( "Show selected permissions only" ) }}
                              </b-form-checkbox>

                            </div>

                            <div class="form-group">
                              <input type="text" class="form-control" v-model="params.overrideSearchQuery" placeholder="{{ ___( 'Search permissions...') }}" />
                            </div>

                          </div>

                          <ul class="main-list">

                            <li v-for="p in searchOverrides" v-show="!params.overrideFilter || ( params.overrideFilter && checkOverride( person, p ) )" class="clickable main-item" :class="{ 'active' : checkOverride( person, p ), 'hidden' : !(!params.overrideFilter || ( params.overrideFilter && checkOverride( person, p ) )) }">

                              <div v-show="!editModeOverrideBranches" class="">

                                <a href="javascript:void(0);" @click="toggleOverride( person, p ); refresh()" class="no-decoration">
                                  <span class="item-name"><i class="fa fa-check text-success" v-show="checkOverride( person, p )"></i><i class="fa fa-times text-danger" v-show="!checkOverride( person, p )"></i> &nbsp; @{{ p.name }}</span>
                                  <span class="item-description">@{{ p.description }}</span>
                                </a>
                                <span class="item-branch-info">
                                  <span v-show="p.branches">@{{ typeof ( p.branches ) !== 'undefined' ? p.branches.length : 0 }} {{ ___('branch(es) selected') }}</span>
                                  <span v-show="!p.branches">{{ ___( 'All branches' ) }}</span>
                                  <span v-show="!editModeOverrideBranches || editOverrideBranchPermissionId != p.id">
                                    <a href="javascript:void(0);" @click="editOverrideBranches(p)"><i class="fa fa-edit"></i></a>
                                  </span>
                                  <span v-show="editModeOverrideBranches && editOverrideBranchPermissionId == p.id">
                                    <a href="javascript:void(0);" @click="cancelEditOverrideBranches()"><i class="fa fa-undo"></i></a>
                                  </span>
                                </span>

                              </div>

                              <div v-show="editModeOverrideBranches && editOverrideBranchPermissionId == p.id" class="branch-list">

                                <h3 class="form-heading"><button class="btn btn-info" @click="cancelEditOverrideBranches()"><i class="fa fa-undo"></i></button> &nbsp; {{ ___('Branches for') }} <strong>@{{vars.current_permission_for_branches.name}}</strong></h3>

                                <div class="form-group push-down">

                                  <b-form-checkbox id="override_branches_filter_check"
                                                 v-model="params.overrideBranchesFilter"
                                                 value="1"
                                                 >
                                    {{ ___( "Show selected branches only" ) }}
                                  </b-form-checkbox>

                                </div>

                                <div class="form-group">
                                  <input type="text" class="form-control" v-model="params.overrideBranchesSearchQuery" placeholder="{{ ___( 'Search branches...') }}" />
                                </div>

                                <div class="no-x-scroll">

                                  <ul class="list-group push-down branch-list-auto-scroll">
                                    <li v-for="b in searchOverrideBranches" class="list-group-item no-border" v-show="!params.overrideBranchesFilter || ( params.overrideBranchesFilter && checkPermissionBranch( person, p, b ) )" >
                                      <a href="javascript:void(0);" @click="toggleOverrideBranch( person, p, b ); refresh()" class="no-decoration">
                                        <span><i class="fa fa-check text-success" v-show="checkPermissionBranch( person, p, b )"></i><i class="fa fa-times text-danger" v-show="!checkPermissionBranch( person, p, b )"></i> @{{ b.name }}</span>
                                      </a>
                                    </li>
                                  </ul>

                                </div>

                              </div>

                            </li>

                          </ul>

                          <div>

                          </div>

                        </div>

                      </div>
            
                    </div>

                  </div>

                  <div v-show="vars.person_edit_tab == 'restrictions'">
                  
                    <div class="form-container">

                      <h3 class="form-heading">{{ ___( 'Permission Restrictions' ) }}</h3>

                      <div class="select-list-container push-down">

                        <div>

                          <div v-show="!editModeRestrictionBranches">
                          
                            <div class="form-group">

                              <b-form-checkbox id="restrictions_filter_check"
                                             v-model="params.restrictionFilter"
                                             value="1"
                                             >
                                {{ ___( "Show selected permissions only" ) }}
                              </b-form-checkbox>

                            </div>

                            <div class="form-group">
                              <input type="text" class="form-control" v-model="params.restrictionSearchQuery" placeholder="{{ ___( 'Search permissions...') }}" />
                            </div>

                          </div>

                          <ul class="main-list">

                            <li v-for="p in searchRestrictions" v-show="!params.restrictionFilter || ( params.restrictionFilter && checkRestriction( person, p ) )" class="clickable main-item" :class="{ 'active' : checkRestriction( person, p ), 'hidden' : !(!params.restrictionFilter || ( params.restrictionFilter && checkRestriction( person, p ) )) }">

                              <div v-show="!editModeRestrictionBranches" class="">

                                <a href="javascript:void(0);" @click="toggleRestriction( person, p ); refresh()" class="no-decoration">
                                  <span class="item-name"><i class="fa fa-check text-success" v-show="checkRestriction( person, p )"></i><i class="fa fa-times text-danger" v-show="!checkRestriction( person, p )"></i> &nbsp; @{{ p.name }}</span>
                                  <span class="item-description">@{{ p.description }}</span>
                                </a>
                                <span class="item-branch-info">
                                  <span v-show="p.branches">@{{ typeof ( p.branches ) !== 'undefined' ? p.branches.length : 0 }} {{ ___('branch(es) selected') }}</span>
                                  <span v-show="!p.branches">{{ ___( 'All branches' ) }}</span>
                                  <span v-show="!editModeRestrictionBranches || editRestrictionBranchPermissionId != p.id">
                                    <a href="javascript:void(0);" @click="editRestrictionBranches(p)"><i class="fa fa-edit"></i></a>
                                  </span>
                                  <span v-show="editModeRestrictionBranches && editRestrictionBranchPermissionId == p.id">
                                    <a href="javascript:void(0);" @click="cancelEditRestrictionBranches()"><i class="fa fa-undo"></i></a>
                                  </span>
                                </span>

                              </div>

                              <div v-show="editModeRestrictionBranches && editRestrictionBranchPermissionId == p.id" class="branch-list">

                                <h3 class="form-heading"><button class="btn btn-info" @click="cancelEditRestrictionBranches()"><i class="fa fa-undo"></i></button> &nbsp; {{ ___('Branches for') }} <strong>@{{vars.current_permission_for_branches.name}}</strong></h3>

                                <div class="form-group push-down">

                                  <b-form-checkbox id="restriction_branches_filter_check"
                                                 v-model="params.restrictionBranchesFilter"
                                                 value="1"
                                                 >
                                    {{ ___( "Show selected branches only" ) }}
                                  </b-form-checkbox>

                                </div>

                                <div class="form-group">
                                  <input type="text" class="form-control" v-model="params.restrictionBranchesSearchQuery" placeholder="{{ ___( 'Search branches...') }}" />
                                </div>

                                <div class="no-x-scroll">

                                  <ul class="list-group push-down branch-list-auto-scroll">
                                    <li v-for="b in searchRestrictionBranches" class="list-group-item no-border" v-show="!params.restrictionBranchesFilter || ( params.restrictionBranchesFilter && checkPermissionBranch( person, p, b ) )" >
                                      <a href="javascript:void(0);" @click="toggleRestrictionBranch( person, p, b ); refresh()" class="no-decoration">
                                        <span><i class="fa fa-check text-success" v-show="checkPermissionBranch( person, p, b )"></i><i class="fa fa-times text-danger" v-show="!checkPermissionBranch( person, p, b )"></i> @{{ b.name }}</span>
                                      </a>
                                    </li>
                                  </ul>

                                </div>

                              </div>

                            </li>

                          </ul>

                          <div>

                          </div>

                        </div>

                      </div>
            
                    </div>

                  </div>

                  <div v-show="vars.person_edit_tab == 'branches'">
                  
                    <div class="form-container">

                      <h3 class="form-heading">{{ ___( 'Branches' ) }}
                        <strong><i v-b-popover.hover="'{{ ___( 'For someone to have permissions to do something at a particular branch, they must have associations with that branch. This is where you specify what branches this person is associated with.' ) }}'" :delay="{ show: 100, hide: 1000 }" class="fa fa-info-circle text-warning popup-icon"></i></strong>
                      </h3>

                      <div class="push-down">

                        <div class="select-list-container">

                          <div class="form-group">

                            <b-form-checkbox id="branch_filter_check"
                                         v-model="params.branchesFilter"
                                         value="1"
                                         >
                              {{ ___( "Show selected branches only" ) }}
                            </b-form-checkbox>

                          </div>

                          <div class="form-group">
                            <input type="text" class="form-control" v-model="params.branchSearchQuery" placeholder="{{ ___( 'Search branches...') }}" />
                          </div>

                          <ul class="main-list push-down">

                            <li v-for="b in searchBranches" v-show="!params.branchesFilter || ( params.branchesFilter && checkBranch( person, b ) )" class="clickable main-item" :class="{ 'active' : checkBranch( person, b ), 'hidden' : !(!params.branchesFilter || ( params.branchesFilter && checkBranch( person, b ) )) }" @click="toggleBranch( person, b ); refresh()">

                              <span class="item-name"><i class="fa fa-check text-success" v-show="checkBranch( person, b )"></i><i class="fa fa-times text-danger" v-show="!checkBranch( person, b )"></i> @{{ b.name }}</span>

                            </li>
           
                          </ul>

                        </div>

                      </div>

                    </div>
        
                  </div>

                  <div v-show="vars.person_edit_tab == 'password'">
                    
                    <div class="form-container">

                      <h3 class="form-heading">{{__('Password Options') }}</h3>

                      <br />

                      <div class="form-group" v-show="person.id > 0">
                        <label for="new_password_visible" v-show="person.show_password">{{ ___( "Set New Password" ) }}:</label>
                        <label for="new_password_hidden" v-show="!person.show_password">{{ ___( "Set New Password" ) }}:</label>
                        <input type="text" class="form-control" id="new_password_visible" v-show="person.show_password" v-model="person.new_password" :disabled="person.generate_password" />
                        <input type="password" class="form-control" id="new_password_hidden" v-show="!person.show_password" v-model="person.new_password" :disabled="person.generate_password" />
                        <span v-show="person.new_password" class="push-down">
                          <a href="javascript:void(0);" @click="person.show_password = !person.show_password; refresh()"><span v-show="person.show_password">{{ ___('Hide Password') }}</span><span v-show="!person.show_password">{{ ___('Show Password') }}</span></a>
                        </span>
                      </div>

                      <div class="form-group">
                      
                        <b-form-checkbox id="generate_password"
                                         v-model="person.generate_password"
                                         value="1"
                                         >
                          {{ ___( "Generate New Password" ) }}
                        </b-form-checkbox>
                      
                      </div>

                      <div class="form-group">
                        
                        <b-form-checkbox id="generate_password_email"
                                         v-model="person.generate_password_email"
                                         :disabled="!person.generate_password && !person.new_password"
                                         value="1"
                                         >
                          {{ ___( "Send an email notification with password" ) }}
                        </b-form-checkbox>
                        
                      </div>

                    </div>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</div>
@endsection

@section('vue-modules')

  <script type="text/javascript">
    include( [ 'Core/People' ] );
    @php if ( $id ?? 0 ): @endphp
    jQuery(document).ready( function() {
    
      People.editPerson( {{ $id }} );
    
    });
    @php endif @endphp
  </script>

@endsection
