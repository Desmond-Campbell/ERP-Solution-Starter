
export const Roles = new Vue({

	el : '#vRoles',
	data : { 
					params : { permissionSearchQuery : '' }, 
					role : { permissions : [] }, 
					roles : [], 
					dirty : false ,
					edit_mode : false,
					edit_id : 0,
					editModePermissions : false,
					errors : {},
					error_targets : {},
					columns : [],
					paging : { page : 1, limit : 10, total : 0, sortIcons : {} },
					mode : 'view',
				},
	
	methods : {

		fetchRole : function ( id ) {

			Progress.start();;
			axios.get( '/api/settings/roles/' + id + '/fetch' ).then( 

				function ( response ) {

					if ( typeof response.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.errors );

					} else {

						Progress.finish();;
						Roles.role = response.data;

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		fetchRoles : function () {

			Progress.start();;
			axios.get( '/api/settings/roles/fetch', { paging : Roles.paging }  ).then( 

				function ( response ) {

					if ( typeof response.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.errors );

					} else {

			            Progress.finish();;
			            Roles.roles = response.data.roles;
									Roles.paging.total = response.data.paging.total;
			            Roles.columns = response.data.columns;

					}

				},

				function () {

					alertError( general_error_failure );

				}

			);

		},

		updateRole : function () {

			Progress.start();;
			axios.post( '/api/settings/roles/' + this.edit_id + '/update', Roles.role ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();

						alertError( response.data.errors );

						if ( response.data.error_details ) {

							Roles.errors = response.data.error_details.messages;
							Roles.error_targets = response.data.error_details.targets;
						
						} else {
							
							Roles.errors = {};
							Roles.error_targets = {};

						}

					} else {

						Progress.finish();;
						Roles.errors = {};
						Roles.error_targets = {};

						Roles.dirty = false;
						Roles.edit_mode = false;

						if ( typeof response.data.role !== 'undefined' ) {

							Roles.role = response.data.role;
							Roles.edit_id = response.data.role.id;

						}

						Roles.fetchRoles();

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		deleteRole : function ( id ) {

			app.$dialog.confirm( ___c( 'Sure you want to delete this role? Everyone with the permissions inherited from this role will be relieved of such permissions, unless they are granted these permissions otherwise, through other roles or override permission settings.' ) ).then( 

				function() {

					Progress.start();;
					axios.post( '/api/settings/roles/' + id + '/delete', Roles.role ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();;
								Roles.dirty = false;
								Roles.edit_mode = false;

								Roles.fetchRoles();

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

		duplicateRole : function ( id ) {

			app.$dialog.confirm( ___c( 'All the permissions on this role will be copied over to the duplicate. Are you sure?' ) ).then( 

				function() {

					Progress.start();;
					axios.post( '/api/settings/roles/' + id + '/duplicate', Roles.role ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();;
								Roles.dirty = false;
								Roles.edit_mode = false;

								Roles.fetchRoles();

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

		editRole : function ( i ) {

			Roles.cancelEditRole();

			Roles.edit_id = i;
			Roles.edit_mode = true;

			if ( i > -1 ) {
			
				Roles.fetchRole( i );

			} else {

				Roles.fetchRole( 'new' );

			}

			Roles.mode = 'edit';

			Roles.refresh();

		},

		cancelEditRole : function () {

			Roles.edit_id = 0;
			Roles.edit_mode = false;
			Roles.role = { permissions : [] };

		},

		checkPermission : function ( role, permission ) {

			var permissions = role.permissions;

			for ( var i = 0; i < permissions.length; i++ ) {

				if ( permissions[i].active && permissions[i].id == permission.id ) return true;

			}

			return false;

		},

		togglePermission : function ( role, permission ) {

			var permissions = role.permissions;

			for ( var i = 0; i < permissions.length; i++ ) {

				if ( permissions[i].id == permission.id ) {

					permissions[i].active = !permissions[i].active;

				}

			}

			Roles.refresh();

		},

		pageChange (page) {

			Roles.paging.page = page;
			Roles.fetchRoles();

		},

		sortBy : function ( field ) {

			if ( Roles.paging.sortField != field ) {
				Roles.paging.sortOrder = 'asc';
			} else {
				Roles.paging.sortOrder = Roles.paging.sortOrder == 'asc' ? 'desc' : 'asc';
			}

			Roles.paging.sortField = field;
			this.sortIcons();
			this.fetchRoles();
			
		},

  	sortIcons : function () {

  		var field = this.paging.sortField;

			var icon = 'down';

			if ( this.paging.sortOrder == 'asc' ) icon = 'up';

			var result = {};
			result[field] = '<i class="fa fa-sort-alpha-' + icon + ' text-info"></i>';

			this.paging.sortIcons = result;

		},

		refresh : function () {

			try {

				Roles.refresh();

			} catch( e ) {
				
			}

		}

	},

	computed: {
    searchPermissions() {

    	try {
	      return this.role.permissions.filter(permission => {
	        return permission.name.toLowerCase().includes(this.params.permissionSearchQuery.toLowerCase())
	      })
	    } catch( e ) {

	    }

    }
  },

	watch : {

		role : {

			handler : function (val, oldVal) {
      
        Roles.dirty = true;
      
      },
      
      deep: true

    }

  }

});

Roles.fetchRoles();

window.Roles = Roles;
