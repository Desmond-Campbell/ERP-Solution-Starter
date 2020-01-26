
export const Branches = new Vue({

	el : '#vBranches',
	
	data : { 
					branch : {}, 
					branches : [], 
					dirty : false ,
					edit_mode : false,
					edit_id : 0,
					errors : {},
					columns : [],
					error_targets : {},
					paging : { page : 1, limit : 10, total : 0, sortIcons : {} }
				},
	
	methods : {

		fetchBranch : function ( id ) {

			Progress.start();;
			axios.get( '/api/settings/branches/' + id + '/fetch' ).then( 

				function ( response ) {

					Progress.finish();;
					Branches.branch = response.data;

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		fetchBranches : function ( id ) {

			Progress.start();;
			axios.post( '/api/settings/branches/fetch', { paging : Branches.paging } ).then( 

				function ( response ) {

					Progress.finish();;
					Branches.branches = response.data.branches;
					Branches.paging.total = response.data.paging.total;
          			Branches.columns = response.data.columns;

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateBranch : function () {

			Progress.start();;
			axios.post( '/api/settings/branches/' + this.edit_id + '/update', Branches.branch ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								Branches.errors = response.data.error_details.messages;
								Branches.error_targets = response.data.error_details.targets;
							
							} else {
								
								Branches.errors = {};
								Branches.error_targets = {};

							}

					} else {

						Progress.finish();;
						Branches.errors = {};
						Branches.error_targets = {};

						Branches.dirty = false;
						Branches.edit_mode = false;
						Branches.fetchBranches();

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		editBranch : function ( i ) {

			Branches.cancelEditBranch();

			Branches.edit_id = i;
			Branches.edit_mode = true;

			if ( i > -1 ) {
			
				Branches.fetchBranch( i );

			} else {

				Branches.branch = {};

			}

		},

		cancelEditBranch : function () {

			Branches.edit_id = 0;
			Branches.edit_mode = false;
			Branches.branch = {};

		},

		enterBranch : function ( id ) {

			location.assign( '/settings/branches/' + id + '/switch??' );

		},

		closeBranch : function ( id ) {

			location.assign( '/settings/branches/' + id + '/close' );

		},

		pageChange (page) {

			Branches.paging.page = page;
			Branches.fetchBranches();

		},

		sortBy : function ( field ) {

			if ( Branches.paging.sortField != field ) {
				Branches.paging.sortOrder = 'asc';
			} else {
				Branches.paging.sortOrder = Branches.paging.sortOrder == 'asc' ? 'desc' : 'asc';
			}

			Branches.paging.sortField = field;
			this.sortIcons();
			this.fetchBranches();
			
		},

  	sortIcons : function () {

  		var field = this.paging.sortField;

			var icon = 'down';

			if ( this.paging.sortOrder == 'asc' ) icon = 'up';

			var result = {};
			result[field] = '<i class="fa fa-sort-alpha-' + icon + ' text-info"></i>';

			this.paging.sortIcons = result;

		}

	},

	watch : {

		branch : {

			handler : function (val, oldVal) {
      
        Branches.dirty = true;
      
      },
      
      deep: true

    },

  },

  computed : {

	}

});

Branches.paging.pages = Math.ceil( Branches.paging.total / Branches.paging.limit );
Branches.fetchBranches();

window.Branches = Branches;
