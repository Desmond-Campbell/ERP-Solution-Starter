import Vue from 'vue';
import axios from 'axios';

export const People = new Vue({

  el : '#vPeople',
  
  data : { 
            params : { roleSearchQuery : '' }, 
            person : { roles : [], permissions : [] }, 
            columns : [],
            people : [], 
            dirty : false ,
            edit_mode : false,
            edit_id : 0,
            editModePermissions : false,
            editModeOverrideBranches : false,
            editModeRestrictionBranches : false,
            errors : {},
            error_targets : {},
            mode : 'view',
            paging : { page : 1, limit : 10, total : 0, sortIcons : {} },
						vars : { person_edit_tab : 'personal', current_permission_for_branches : { branches : [] } },
        },

  methods : {

      fetchPerson : function ( id ) {

          Progress.start();
          axios.get( '/api/settings/people/' + id + '/fetch' ).then( 

              function ( response ) {

                  if ( typeof response.errors !== 'undefined' ) {

                      Progress.fail();
                      alertError( response.errors );

                  } else {

                      Progress.finish();;
                      People.person = response.data;

                  }

              },

              function () {

                  Progress.fail();
                  alertError( general_error_failure );

              }

          );

      },

      fetchPeople : function () {

          Progress.start();
          axios.post( '/api/settings/people/fetch', { paging : People.paging } ).then( 

              function ( response ) {

                  if ( typeof response.errors !== 'undefined' ) {

                      Progress.fail();
                      alertError( response.errors );

                  } else {

                    Progress.finish();;
                    People.people = response.data.people;
                    People.columns = response.data.columns;
										People.paging.total = response.data.paging.total;

                  }

              },

              function () {

                  Progress.fail();
                  alertError( general_error_failure );

              }

          );

      },

      editOverrideBranches : function( permission ) {

          People.vars.current_permission_for_branches = permission;
          People.cancelEditOverrideBranches();
          People.editModeOverrideBranches = true;
          People.editOverrideBranchPermissionId = permission.id;

      },

      cancelEditOverrideBranches : function() {

          People.editModeOverrideBranches = false;
          People.editOverrideBranchPermissionId = -2;

      },

      editRestrictionBranches : function( permission ) {

          People.editModeRestrictionBranches = true;
          People.editRestrictionBranchPermissionId = permission.id;

      },

      cancelEditRestrictionBranches : function() {

          People.editModeRestrictionBranches = false;
          People.editRestrictionBranchPermissionId = -2;

      },

      updatePerson : function () {

          Progress.start();
          axios.post( '/api/settings/people/' + this.edit_id + '/update', People.person ).then( 

              function ( response ) {

                  if ( typeof response.data.errors !== 'undefined' ) {

                    Progress.fail();
                    alertError( response.data.errors );

                    if ( response.data.error_details ) {

                      People.errors = response.data.error_details.messages;
                      People.error_targets = response.data.error_details.targets;
                    
                    } else {
                      
                      People.errors = {};
                      People.error_targets = {};

                    }

                  } else {

                      Progress.finish();;
                      People.errors = {};
                      People.error_targets = {};

                      People.dirty = false;
                      People.edit_mode = false;

                      if ( typeof response.data.user !== 'undefined' ) {

                        People.person = response.data.user;
                        People.edit_id = response.data.user.id;

                      }

                  }

              },

              function () {

                Progress.fail();
                alertError( general_error_failure );

              }

          );

      },

      deletePerson : function ( id ) {

          app.$dialog.confirm( ___c( 'Are you 100% sure you want to delete this person? You WILL NOT be able to recover, once deleted. Think twice.' ) ).then( 

            function() {

                Progress.start();
                axios.post( '/api/settings/people/' + id + '/delete', People.person ).then( 

                      function ( response ) {

                          if ( typeof response.data.errors !== 'undefined' ) {

                              Progress.fail();
                              alertError( response.data.errors );

                          } else {

                              Progress.finish();;
                              People.dirty = false;
                              People.edit_mode = false;

                              People.fetchPeople();

                          }

                      },

                      function () {

                        Progress.fail();
                        alertError( general_error_failure );

                      }

                  );

            }

          );

      },

      editPerson : function ( i ) {

          People.cancelEditPerson();

          People.edit_id = i;
          People.edit_mode = true;

          if ( i > -1 ) {
          
              People.fetchPerson( i );

          } else {

              People.fetchPerson( 'new' );

          }

          People.mode = 'edit';

          People.refresh();

      },

      cancelEditPerson : function () {

          People.edit_id = 0;
          People.edit_mode = false;
          People.person = {};
          People.vars.person_edit_tab = 'personal';

      },

      checkRole : function ( person, role ) {

          var roles = person.roles;

          for ( var i = 0; i < roles.length; i++ ) {

              if ( roles[i].active && roles[i].id == role.id ) return true;

          }

          return false;

      },

      toggleRole : function ( person, role ) {

          var roles = person.roles;

          for ( var i = 0; i < roles.length; i++ ) {

              if ( roles[i].id == role.id ) {

                  roles[i].active = !roles[i].active;

              }

          }

          People.refresh();

      },

      checkOverride : function ( person, permission ) {

          var permissions = person.permission_overrides;

          for ( var i = 0; i < permissions.length; i++ ) {

              if ( permissions[i].active && permissions[i].id == permission.id ) return true;

          }

          return false;

      },

      toggleOverride : function ( person, permission ) {

          var permissions = person.permission_overrides;

          for ( var i = 0; i < permissions.length; i++ ) {

              if ( permissions[i].id == permission.id ) {

                  permissions[i].active = !permissions[i].active;

              }

          }

          People.refresh();

      },

      checkRestriction : function ( person, permission ) {

          var permissions = person.permission_restrictions;

          for ( var i = 0; i < permissions.length; i++ ) {

              if ( permissions[i].active && permissions[i].id == permission.id ) return true;

          }

          return false;

      },

      toggleRestriction : function ( person, permission ) {

          var permissions = person.permission_restrictions;

          for ( var i = 0; i < permissions.length; i++ ) {

              if ( permissions[i].id == permission.id ) {

                  permissions[i].active = !permissions[i].active;

              }

          }

          People.refresh();

      },

      checkBranch : function ( person, branch ) {

          var Branches = person.branches;

          for ( var i = 0; i < Branches.length; i++ ) {

              if ( Branches[i].active && Branches[i].id == branch.id ) return true;

          }

          return false;

      },

      toggleBranch : function ( person, branch ) {

          var Branches = person.branches;

          for ( var i = 0; i < Branches.length; i++ ) {

              if ( Branches[i].id == branch.id ) {

                  Branches[i].active = !Branches[i].active;

              }

          }

          People.refresh();

      },

      toggleOverrideBranch : function ( person, permission, branch ) {

          permission.branches = typeof permission.branches !== 'undefined' ? permission.branches : [];

          var found = false;

          for ( var i = 0; i < permission.branches.length; i++ ) {

              if ( permission.branches[i].id == branch.id ) {

                  permission.branches[i].active = !permission.branches[i].active;

                  found = true;

              }

          }

          if ( !found ) {

              branch.active = !branch.active;
              permission.branches.push( branch );

          }

          for ( var j = 0; j < People.person.permission_overrides.length; j++ ) {

              if ( People.person.permission_overrides[j].id == permission.id ) {

                  People.person.permission_overrides[j].branches = permission.branches;

              }

          }

          People.refresh();

      },

      toggleRestrictionBranch : function ( person, permission, branch ) {

          permission.branches = typeof permission.branches !== 'undefined' ? permission.branches : [];

          var found = false;

          for ( var i = 0; i < permission.branches.length; i++ ) {

              if ( permission.branches[i].id == branch.id ) {

                  permission.branches[i].active = !permission.branches[i].active;

                  found = true;

              }

          }

          if ( !found ) {

              branch.active = !branch.active;
              permission.branches.push( branch );

          }

          for ( var j = 0; j < People.person.permission_restrictions.length; j++ ) {

              if ( People.person.permission_restrictions[j].id == permission.id ) {

                  People.person.permission_restrictions[j].branches = permission.branches;

              }

          }

          People.refresh();

      },

      checkPermissionBranch : function ( person, permission, branch ) {

          if ( typeof branch !== 'undefined' ) {

              var Branches = typeof permission.branches !== 'undefined' ? permission.branches : [];

              if ( Branches === [] || typeof Branches === 'undefined' ) return false;

              if ( typeof Branches === 'undefined' ) return false;

              for ( var i = 0; i < Branches.length; i++ ) {

                  if ( typeof Branches[i] !== 'undefined' ) {

                      if ( Branches[i].active && Branches[i].id == branch.id ) {

                          return true;

                      }

                  }

              }

          }

          return false;

      },

      queryUpdated : function (updated) {

      },

			pageChange (page) {

				People.paging.page = page;
				People.fetchPeople();

			},

			sortBy : function ( field ) {

				if ( People.paging.sortField != field ) {
					People.paging.sortOrder = 'asc';
				} else {
					People.paging.sortOrder = People.paging.sortOrder == 'asc' ? 'desc' : 'asc';
				}

				People.paging.sortField = field;
				this.sortIcons();
				this.fetchPeople();
				
			},

	  	sortIcons : function () {

	  		var field = this.paging.sortField;

				var icon = 'down';

				if ( this.paging.sortOrder == 'asc' ) icon = 'up';

				var result = {};
				result[field] = '<i class="fa fa-sort-alpha-' + icon + ' text-info"></i>';

				this.paging.sortIcons = result;

			},

			setPersonEditTab : function (tab) {
				People.vars.person_edit_tab = tab;
			},

      refresh : function () {

          try {

              People.refresh();

          } catch( e ) {
              
          }

      },

  },

  computed: {
    searchRoles() {

        try {
          return this.person.roles.filter(role => {
            return !this.params.roleSearchQuery || role.name.toLowerCase().includes(this.params.roleSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    },
    searchOverrides() {

        try {
          return this.person.permission_overrides.filter(permission => {
            return !this.params.overrideSearchQuery || permission.name.toLowerCase().includes(this.params.overrideSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    },
    searchRestrictions() {

        try {
          return this.person.permission_restrictions.filter(permission => {
            return !this.params.restrictionSearchQuery || permission.name.toLowerCase().includes(this.params.restrictionSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    },
    searchBranches() {

        try {
          return this.person.branches.filter(branch => {
            return !this.params.branchSearchQuery || branch.name.toLowerCase().includes(this.params.branchSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    },
    searchOverrideBranches() {

        try {
          return this.person.branches.filter(branch => {
            return !this.params.overrideBranchesSearchQuery || branch.name.toLowerCase().includes(this.params.overrideBranchesSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    },
    searchRestrictionBranches() {

        try {
          return this.person.branches.filter(branch => {
            return !this.params.restrictionBranchesSearchQuery || branch.name.toLowerCase().includes(this.params.restrictionBranchesSearchQuery.toLowerCase());
          })
        } catch( e ) {

        }

    }
	},

  watch : {

    person : {

        handler : function (val, oldVal) {
  
        People.dirty = true;
      
      },
    
    	deep: true

  	}

	},

	mounted () {
	}

});

window.People = People;
People.fetchPeople();
