import Vue from 'vue';
import axios from 'axios';

import { Chrome } from 'vue-color';

export const Branding = new Vue({

	el : '#vBranding',
	
	data : { 
					branding : {
						header_text_colour : {
						  hex: '#194d33',
						},
						header_background_colour : {
						  hex: '#194d33',
						},
						title_background_colour : {
						  hex: '#E2ECF7',
						},
						title_text_colour : {
						  hex: '#000000',
						},
						company_name_colour : {
						  hex: '#000000',
						},
					}, 
					dirty : false ,
					edit_mode : '',
					edit_id : 0,
					edit_item : {},
					mode : 'view',
					lists : {},
					errors : {},
					error_targets : {},
					logo : { files : [] },
					favicon : { files : [] },
					vars : { branding_edit_tab : 'logo', colour_edit_mode : '' },
					cache : {}
				},

	components: {
    'chrome-picker': Chrome
  },
	
	methods : {

		fetchBranding : function () {

			Progress.start();;
			axios.get( '/api/settings/branding/fetch' ).then( 

				function ( response ) {

					Progress.finish();;
					Branding.branding = response.data;
					Branding.dirty = false;

					Branding.clean();

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		fetchBrandingImages : function () {

			Progress.start();;
			axios.get( '/api/settings/branding/fetch-images' ).then( 

				function ( response ) {

					Progress.finish();;
					Branding.branding.logo_path = response.data.logo_path || '';
					Branding.branding.favicon_path = response.data.favicon_path || '';
					Branding.dirty = false;

					Branding.clean();

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		updateBranding : function () {

			Progress.start();;
			axios.post( '/api/settings/branding/update', Branding.branding ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

						if ( response.data.error_details ) {

							Branding.errors = response.data.error_details.messages;
							Branding.error_targets = response.data.error_details.targets;
						
						} else {
							
							Branding.errors = {};
							Branding.error_targets = {};

						}

					} else {

						Progress.finish();;
						Branding.errors = {};
						Branding.error_targets = {};

						Branding.dirty = false;

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		},

		removeLogo : function () {

			app.$dialog.confirm( ___c( 'Remove this logo for sure? Sure you can add this logo again or add another, but you will not be able to automatically "undo" this delete action you are about to confirm.' ) ).then( 

				function() {

					Progress.start();;
					axios.post( '/api/settings/branding/remove-logo' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();;
								Branding.fetchBrandingImages();

							}

						},

						function () {

							alertError( general_error_failure );

						}

					);

				}

			);

		},

		removeFavicon : function () {

			app.$dialog.confirm( ___c( 'Do you really want to remove this favicon? You will have to add it (or another one) manually again.' ) ).then( function() {

					Progress.start();;
					axios.post( '/api/settings/branding/remove-favicon' ).then( 

						function ( response ) {

							if ( typeof response.data.errors !== 'undefined' ) {

								Progress.fail();
								alertError( response.data.errors );

							} else {

								Progress.finish();;
								Branding.fetchBrandingImages();

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

		clean : function () {
			Branding.dirty = false;
		},

		setBrandingEditTab : function (tab) {
			Branding.vars.branding_edit_tab = tab;
		},

		setColourMode : function (mode) {
			Branding.vars.colour_edit_mode = mode;
		},

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
        if (!/\.(jpeg|jpe|jpg|gif|png|webp)$/i.test(newFile.name)) {
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

    refresh : function () {

    	try {

				CompanyInfo.refresh();

			} catch( e ) {
				
			}

    }

	},

	watch : {

		branding : {

			handler : function (val, oldVal) {
      
        Branding.dirty = true;
      
      },
      
      deep: true

    },

    favicon : {

    	handler : function (val, oldVal) {
      
        var finished = false;

        for ( var f = 0; f < Branding.favicon.files.length; f++ ) {

        	if ( Branding.favicon.files[f].success ) {

        		finished = true;
						Branding.favicon.files.splice( f, 1 );

        	}

        }

        if ( finished ) {

        	Branding.fetchBrandingImages();

        }
      
      },

      deep : true

    },

    logo : {

    	handler : function (val, oldVal) {
      
        var finished = false;

        for ( var f = 0; f < Branding.logo.files.length; f++ ) {

        	if ( Branding.logo.files[f].success ) {

        		finished = true;
						Branding.logo.files.splice( f, 1 );

        	}

        }

        if ( finished ) {

        	Branding.fetchBrandingImages();

        }
      
      },

      deep : true

    }

  }

});

Branding.fetchBranding();

window.Branding = Branding;
