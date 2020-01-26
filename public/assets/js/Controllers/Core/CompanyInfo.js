import Vue from 'vue';
import axios from 'axios';

export const CompanyInfo = new Vue({

	el : '#vCompanyInfo',
	
	data : { 
					company : { documents : [] }, 
					dirty : false ,
					edit_mode : '',
					edit_id : {
							address : -2,
							phone_number : -2,
							director : -2,
							email : -2,
							document : -2,
						},
					edit_item : { 
							address : { _capture : 1 }, 
							phone_number : { _capture : 1 }, 
							director : { _capture : 1 }, 
							email : { _capture : 1 }, 
							document : { _capture : 1 }, 
					},
					mode : 'view',
					errors : {},
					error_targets : {},
					lists : {},
					vars : { company_edit_tab : 'details' },
					upload : { files : [] },
					cache : { address : JSON.stringify( {} ), 
										phone_number : JSON.stringify( {} ),
										director : JSON.stringify( {} ),
										email : JSON.stringify( {} ),
										document : JSON.stringify( {} ),
									 },
				},

  	methods : {

		fetchCompany : function () {

			Progress.start();

			axios.get( '/api/settings/company/fetch' ).then( 

				function ( response ) {

					Progress.finish();

					CompanyInfo.company = response.data;
					// CompanyInfo.fetchDocuments();
					CompanyInfo.dirty = false;
					CompanyInfo.errors = {};
					CompanyInfo.error_targets = {};

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateCompany : function ( section ) {

			CompanyInfo.company.section = section;

			Progress.start();
			
			axios.post( '/api/settings/company/update', CompanyInfo.company ).then( 

				function ( response ) {

					if ( response.data.errors ) {

						Progress.fail();
						alertError( response.data.errors );

						if ( response.data.error_details ) {
							
							CompanyInfo.errors = response.data.error_details.messages;
							CompanyInfo.error_targets = response.data.error_details.targets;
						
						} else {
							
							CompanyInfo.errors = {};
							CompanyInfo.error_targets = {};
						
						}

					} else {

						Progress.finish();
						
						CompanyInfo.fetchCompany();
						CompanyInfo.dirty = false;

						CompanyInfo.errors = {};
						CompanyInfo.error_targets = {};

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		///////////////

			editItem : function ( item, i ) {

				var items = item + 's';
				if ( item == 'address' ) items = 'addresses';
				CompanyInfo.cancelChangeItem( item );
				
				CompanyInfo.edit_id[item] = i;
				CompanyInfo.edit_mode = item;

				if ( i > -1 ) {
				
					var e = { data : [] };
					var list = CompanyInfo.company[items][i];

					e.data.push( list );

					var x = e.data[0];
					CompanyInfo.edit_item[item] = x;

					CompanyInfo.cache[item] = JSON.stringify(x);

				}

			},

			changeItem : function ( item ) {

				var data = {};
				var items = item + 's';
				if ( item == 'address' ) items = 'addresses';
				var section = 'company_' + items;
				data[section] = [ CompanyInfo.edit_item[item] ];
				data['section'] = section;

				Progress.start();
				
				axios.post( '/api/settings/company/validate', data ).then( 

					function ( response ) {

						if ( response.data.errors ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								CompanyInfo.errors = response.data.error_details.messages;
								CompanyInfo.error_targets = response.data.error_details.targets;
							
							} else {
								
								CompanyInfo.errors = {};
								CompanyInfo.error_targets = {};

							}

						} else {

							Progress.finish();
							
							CompanyInfo.errors = {};
							CompanyInfo.error_targets = {};

							var i = CompanyInfo.edit_id[item];
							CompanyInfo.edit_id[item] = -2;
							CompanyInfo.edit_mode = '';

							var e = CompanyInfo.edit_item[item];
							CompanyInfo.edit_item[item] = { _capture : 1 };

							if ( i > -1 ) {

								CompanyInfo.company[items][i] = e;

							} else {

								CompanyInfo.company[items].push( e );

							}

							CompanyInfo.cache[item] = JSON.stringify({});

							CompanyInfo.updateCompany( section );
						
						}

					},

					function () {

						Progress.fail();
						alertError( general_error_failure );

					}

				);

			},

			cancelChangeItem : function ( item ) {

				var items = item + 's';
				if ( item == 'address' ) items = 'addresses';
				
				if ( CompanyInfo.edit_id[item] > -2 && typeof CompanyInfo.cache[item] !== 'undefined' ) {
					CompanyInfo.company[items][CompanyInfo.edit_id[item]] = JSON.parse(CompanyInfo.cache[item]);
				}

				CompanyInfo.edit_id[item] = -2;
				CompanyInfo.edit_mode = '';
				CompanyInfo.edit_item[item] = { _capture : 1 };

			},

			deleteItem : function ( item, i ) {

				var items = item + 's';
				if ( item == 'address' ) items = 'addresses';
				
				app.$dialog.confirm( ___c( 'Are you sure you want to delete this ' + item + '?' ) ).then( 

					function() {

						CompanyInfo.company[items].splice( i, 1 );

					}

				);


			},

		setCompanyEditTab : function (tab) {
			CompanyInfo.vars.company_edit_tab = tab;
		},

		// Documents

			/**
	     * Has changed
	     * @param  Object|undefined   newFile   Read only
	     * @param  Object|undefined   oldFile   Read only
	     * @return undefined
	     */
	    inputFile: function (newFile, oldFile) {
	      if (newFile && oldFile && !newFile.active && oldFile.active) {
	        // Get response data
	        if (newFile.xhr) {
	          //  Get the response status code
	        }
	      }
	    },
	    /**
	     * Pretreatment
	     * @param  Object|undefined   newFile   Read and write
	     * @param  Object|undefined   oldFile   Read only
	     * @param  Function           prevent   Prevent changing
	     * @return undefined
	     */
	    inputFilter: function (newFile, oldFile, prevent) {
	      if (newFile && !oldFile) {
	        // Filter non-image file
	        if (!/\.*$/i.test(newFile.name)) {
	          return prevent()
	        }
	      }

	      // Create a blob field
	      newFile.blob = ''
	      let URL = window.URL || window.webkitURL
	      if (URL && URL.createObjectURL) {
	        newFile.blob = URL.createObjectURL(newFile.file)
	      }

	    },

		fetchDocuments : function () {

			Progress.start();
		  	
		  	axios.get( '/api/settings/company/documents/fetch' ).then( 

					function ( response ) {

						if ( response.data.errors ) {

							Progress.fail();
							alertError( response.data.errors );

						} else {

							Progress.finish();
							CompanyInfo.dirty = false;
							CompanyInfo.refresh();
							CompanyInfo.company.documents = response.data;

						}

					},

					function () {

						Progress.fail();
						alertError( general_error_failure );

					}

				);

		  },

		  editDocument : function ( index ) {

		  	var doc = CompanyInfo.company.documents[index];

		  	if ( typeof doc !== 'undefined' ) {

		  		CompanyInfo.cache.document = JSON.stringify( doc );
		  		CompanyInfo.cache.document_id = doc.id;

		  	}

		  	CompanyInfo.refresh();

		  },

		  cancelEditDocument : function ( index ) {

	  		if ( typeof CompanyInfo.cache.document === 'undefined' ) {

	  			CompanyInfo.cache.document =  JSON.stringify({});

	  		}

	  		CompanyInfo.company.documents[index] = JSON.parse( CompanyInfo.cache.document );
	  		CompanyInfo.cache.document = JSON.stringify( {} );
	  		CompanyInfo.cache.document_id = -2;

	  		CompanyInfo.fetchDocuments();

		  },

		  updateDocument : function ( doc ) {

			Progress.start();
		  	
		  	axios.post( '/api/settings/company/documents/' + doc.id + '/update', doc ).then( 

					function ( response ) {

						if ( response.data.errors ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								CompanyInfo.errors = response.data.error_details.messages;
								CompanyInfo.error_targets = response.data.error_details.targets;
							
							} else {
								
								CompanyInfo.errors = {};
								CompanyInfo.error_targets = {};

							}

						} else {

							Progress.finish();
							CompanyInfo.errors = {};
							CompanyInfo.error_targets = {};

							CompanyInfo.cancelEditDocument();

						}

					},

					function () {

						Progress.fail();
						alertError( general_error_failure );

					}

				);

		  },

		  archiveDocument : function ( id ) {

			Progress.start();
		  	
		  	axios.post( '/api/settings/company/documents/' + id + '/archive' ).then( 

					function ( response ) {

						if ( response.data.errors ) {

							Progress.fail();
							alertError( response.data.errors );

							if ( response.data.error_details ) {

								CompanyInfo.errors = response.data.error_details.messages;
								CompanyInfo.error_targets = response.data.error_details.targets;
							
							} else {
								
								CompanyInfo.errors = {};
								CompanyInfo.error_targets = {};

							}

						} else {

							Progress.finish();
							CompanyInfo.fetchDocuments();

						}

					},

					function () {

						Progress.fail();
						alertError( general_error_failure );

					}

				);

		  },

		  deleteDocument : function ( id ) {

			app.$dialog.confirm( ___c( 'Are you sure you want to delete this document permanently?' ) ).then( 

				function() {

					Progress.start();
				  	
				  	axios.delete( '/api/settings/company/documents/' + id + '/delete' ).then( 

						function ( response ) {

							if ( response.data.errors ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();
								CompanyInfo.fetchDocuments();

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

	  getSection : function ( section ) {

	  	CompanyInfo.mode = 'edit';
	  	CompanyInfo.vars.company_edit_tab = section;
	  	CompanyInfo.refresh();

	  },

	  moment : function ( date ) {

	  	return moment( date );

	  },

		refresh : function () {

			try {

				CompanyInfo.refresh();

			} catch( e ) {
				
			}

		}

	},

	watch : {

		company : {

			handler : function (val, oldVal) {
      
        CompanyInfo.dirty = true;
      
      },
      
      deep: true

    },

    upload : {

    	handler : function (val, oldVal) {
      
        var finished = false;

        for ( var f = 0; f < CompanyInfo.upload.files.length; f++ ) {

        	if ( CompanyInfo.upload.files[f].success ) {

        		finished = true;
				CompanyInfo.upload.files.splice( f, 1 );

        	}

        }

        if ( finished ) {

        	CompanyInfo.fetchDocuments();

        }
      
      },

      deep : true

    }

  }

});

// Fetch list items

axios.get( '/api/lists/fetch?lists=company_types' ).then( 

	function ( response ) {

		CompanyInfo.lists = response.data;

	},

	function () {

	}

);

window.CompanyInfo = CompanyInfo;
window.CompanyInfo.fetchCompany();
