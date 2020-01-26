
export const Lists = new Vue({

	el : '#vLists',
	
	data : { 
					field : {}, 
					section : 'manage',
					list : { show_fields : false, id : 0, list_items : [] }, 
					lists : [], 
					current_lists_id : 0,
					lists_filtered : [],
					list_items : { vertical : [], horizontal : [] }, 
					list_items_paging : { page : 1, limit : 10, total : 0, sortIcons : {} },
					list_item_data : {},
					data_layout_mode : 1,
					data_view_mode : 'horizontal',
					data_columns : [],
					edit_item_data_id : 0,
					edit_item_lists_id : 0,
					list_info : {},
					category_filter : 0, 
					categories : [], 
					category : {}, 
					dirty : false ,
					edit_mode : false,
					edit_id : -2,
					edit_category_id : -2,
					edit_category_mode : false,
					edit_field_mode : false,
					edit_field_id : -2,
					edit_data_mode : false,
					edit_data_id : -2,
					errors : {},
					columns : [],
					error_targets : {},
					paging : { page : 1, limit : 10, total : 0, sortIcons : {} }
				},
	
	methods : {

		fetchCategories : function () {

			Progress.start();
			axios.get( '/api/settings/lists/categories/fetch').then( 

				function ( response ) {

					Progress.finish();
					Lists.categories = response.data;

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateCategory : function () {

			Progress.start();
			axios.post( '/api/settings/lists/categories/' + this.category.id + '/update', Lists.category ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

						if ( response.data.error_details ) {

							Lists.errors = response.data.error_details.messages;
							Lists.error_targets = response.data.error_details.targets;
						
						} else {
							
							Lists.errors = {};
							Lists.error_targets = {};

						}

					} else {

						Progress.finish();
						Lists.errors = {};
						Lists.error_targets = {};

						Lists.fetchCategories();
						Lists.cancelEditCategory();

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		editCategory : function ( i ) {

			Lists.cancelEditCategory();

			Lists.edit_category_id = i;
			Lists.edit_category_mode = true;

			if ( i > -1 ) {
			
				Lists.category = Lists.categories[i];

			} else {

				Lists.category = {};

			}

		},

		deleteCategory : function ( index ) {

			app.$dialog.confirm( ___c( "We'll attempt to delete this category and transfer all the lists to the default category. OK?" ) ).then( 

				function() {

					Progress.start();
					axios.delete( '/api/settings/lists/categories/' + Lists.categories[index].id + '/delete' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();
								Lists.fetchCategories();

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

		cancelEditCategory : function () {

			Lists.edit_category_id = -2;
			Lists.edit_category_mode = false;
			Lists.category = {};

		},	

		fetchList : function ( id ) {

			Progress.start();
			axios.get( '/api/settings/lists/' + id + '/fetch' ).then( 

				function ( response ) {

					Progress.finish();
					Lists.list = response.data;

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		fetchLists : function ( id ) {

			Progress.start();
			axios.post( '/api/settings/lists/fetch', { paging : Lists.paging, category_filter : Lists.category_filter } ).then( 

				function ( response ) {

					Progress.finish();
					if ( Lists.category_filter ) {

						Lists.lists_filtered = response.data.lists;
						Lists.lists = response.data.lists;

					} else {					
					
						Lists.lists = response.data.lists;

						for ( var l = 0; l < Lists.lists.length; l++ ) {

							if ( Lists.list.id == Lists.lists[l].id ) {

								Lists.list.show_fields = Lists.list.show_fields;

							}

						}

						Lists.paging.total = response.data.paging.total;

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateList : function () {

			if ( !Lists.category_filter && !this.list.id ) {

				alertError( ___t( 'Please select a category from the left.' ) );

			} else {

				if ( !this.list.id ) {

					Lists.list.category_id = Lists.category_filter;

				}

				Progress.start();
				axios.post( '/api/settings/lists/' + this.list.id + '/update', Lists.list ).then( 

					function ( response ) {

						if ( typeof response.data.errors !== 'undefined' ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								Lists.errors = response.data.error_details.messages;
								Lists.error_targets = response.data.error_details.targets;
							
							} else {
								
								Lists.errors = {};
								Lists.error_targets = {};

							}

						} else {

							Progress.finish();
							Lists.errors = {};
							Lists.error_targets = {};

							Lists.dirty = false;
							
							if ( response.data.list ) {

								response.data.list.show_fields = Lists.list.show_fields;
								Lists.list = response.data.list;

							}

							alertInfo( ___t('List was updated successfully.') );

						}

					},

					function () {

						Progress.fail();
						alertError( general_error_failure );

					}

				);

			}

		},

		deleteList : function ( id ) {

			app.$dialog.confirm( ___c( "Are you sure you ant to delete this list and EVERYTHING associated with it? That's the only way we can delete -  a full, comprehensive delete." ) ).then( 

					function() {

						Progress.start();
						axios.delete( '/api/settings/lists/' + id + '/delete' ).then( 

							function ( response ) {

								if ( typeof response.data.errors !== 'undefined' ) {

									Progress.fail();
									alertError( response.data.errors );

								} else {

									Progress.finish();
									Lists.fetchLists();

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

		emptyList : function ( id ) {

			app.$dialog.confirm( ___c( 'Delete the entries in this list PEMANENTLY?' ) ).then( 

				function() {

					Progress.start();
					axios.delete( '/api/settings/lists/' + id + '/empty' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();
								Lists.fetchLists();

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

		editList : function ( i ) {

			Lists.cancelEditList();

			Lists.edit_id = i;
			Lists.edit_mode = true;
			Lists.section = 'manage';

			if ( i > -1 ) {
			
				Lists.list = Lists.lists[i];

			} else {

				Lists.list = {  show_fields : false, id : 0, list_items : [] };

			}

		},

		cancelEditList : function () {

			Lists.edit_id = 0;
			Lists.edit_mode = false;
			Lists.list = {};

		},

		updateListField : function () {

			Progress.start();
			axios.post( '/api/settings/lists/' + this.list.id + '/fields/' + this.field.id + '/update', Lists.field ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

						if ( response.data.error_details ) {

							Lists.errors = response.data.error_details.messages;
							Lists.error_targets = response.data.error_details.targets;
						
						} else {
							
							Lists.errors = {};
							Lists.error_targets = {};

						}

					} else {

						Progress.finish();
						Lists.errors = {};
						Lists.error_targets = {};

						Lists.dirty = false;

						if ( response.data.fields ) {

							Lists.list.fields = response.data.fields;
							Lists.edit_field_mode = false;

						}

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		changeFieldStatus : function ( id ) {

			Progress.start();
			axios.post( '/api/settings/lists/fields/' + id + '/update-status' ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

					} else {

						Progress.finish();
						if ( response.data.status ) {

							Lists.field.status = response.data.status;

						}

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		deleteField : function ( id ) {

			app.$dialog.confirm( ___c( "Delete this field? If there's data tied to it, we'll have to delete that data as well. Sure you want to continue?" ) ).then( 

				function() {

					Progress.start();
					axios.delete( '/api/settings/lists/fields/' + id + '/delete' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();
								Lists.list.fields.splice( index, 1 );

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

		editField : function ( i ) {

			Lists.cancelEditField();

			Lists.edit_field_id = i;
			Lists.edit_field_mode = true;

			if ( i > -1 ) {
			
				Lists.field = Lists.list.fields[i];

			} else {

				Lists.field = {   };

			}

		},

		cancelEditField : function () {

			Lists.edit_field_id = -2;
			Lists.edit_field_mode = false;
			Lists.field = {  };

		},

		pageChange (page) {

			Lists.paging.page = page;
			Lists.fetchLists();

		},

		fromJSON : function( string ) {

			return JSON.parse( window.atob( string ) );

		},

		fetchListData : function ( lists_id ) {

			Lists.current_lists_id = lists_id;

			Progress.start();
			axios.get( '/api/settings/lists/' + lists_id + '/fetch-data', { paging : Lists.list_items_paging } ).then( 

				function ( response ) {

					Progress.finish();
					Lists.list_items = response.data.list_items;
					
					Lists.list_items_paging.total = response.data.paging.total;
					Lists.data_columns = response.data.columns;
					Lists.list_info = response.data.list_info;

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		editListItemData : function ( lists_id, item_id ) {

			Lists.edit_item_data_id = item_id;
			Lists.edit_item_lists_id = lists_id;

			Progress.start();
			axios.get( '/api/settings/lists/' + lists_id + '/item-data/' + item_id + '/fetch' ).then( 

				function ( response ) {

					Progress.finish();
					Lists.list_item_data = response.data;
					Lists.edit_data_mode = true;
					
				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateListItemData : function () {

			Progress.start();
			axios.post( '/api/settings/lists/' + Lists.edit_item_lists_id + '/item-data/' + Lists.edit_item_data_id + '/update', { data : Lists.list_item_data } ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								Lists.errors = response.data.error_details.messages;
								Lists.error_targets = response.data.error_details.targets;
							
							} else {
								
								Lists.errors = {};
								Lists.error_targets = {};

							}

					} else {

						Progress.finish();
						Lists.fetchListData(Lists.edit_item_lists_id);
						
						Lists.errors = {};
						Lists.error_targets = {};

						Lists.edit_item_lists_id = 0;
						Lists.edit_item_data_id = 0;
						Lists.list_item_data = {};
						Lists.edit_data_mode = false;


					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		cancelUpdateListItemData : function () {

			Lists.fetchListData(Lists.edit_item_lists_id);
						
			Lists.errors = {};
			Lists.error_targets = {};

			Lists.edit_item_lists_id = 0;
			Lists.edit_item_data_id = 0;
			Lists.list_item_data = {};
			Lists.edit_data_mode = false;

		},

		deleteListItem : function ( lists_id, id ) {

			app.$dialog.confirm( ___c( 'Are you sure you want to delete this PERMANENTLY?' ) ).then( 

				function() {

					Progress.start();
					axios.delete( '/api/settings/lists/item-data/' + id + '/delete' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();
								Lists.fetchListData(lists_id);
								Lists.edit_data_mode = false;

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

	},

	watch : {

		list : {

			handler : function (val, oldVal) {
      
        Lists.dirty = true;
      
      },
      
      deep: true

    },

  },

  computed : {

	}

});

Lists.fetchCategories();

Lists.paging.pages = Math.ceil( Lists.paging.total / Lists.paging.limit );
Lists.fetchLists();

window.Lists = Lists;
