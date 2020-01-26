import axios from '../../js/node_modules/axios';

export const Account = new Vue({

  el : '#vAccount',
  
  data : { 
            params : {}, 
            account : {}, 
            dirty : false ,
            edit_mode : false,
            edit_id : 0,
            errors : {},
            error_targets : {},
            avatar : { files : [] },
						vars : { account_edit_tab : 'account_personal' },
        },

  methods : {

      fetchAccount : function ( id ) {

        Progress.start();;
        axios.get( '/api/account/fetch' ).then( 

          function ( response ) {

            if ( typeof response.errors !== 'undefined' ) {

              Progress.fail();
              alertError( response.errors );

            } else {

              Progress.finish();;
              Account.account = response.data;

            }

          },

          function () {

            Progress.fail();
            alertError( general_error_failure );

          }

        );

      },

      updateAccount : function () {

        Progress.start();;
        axios.post( '/api/account/update', Account.account ).then( 

          function ( response ) {

            if ( typeof response.data.errors !== 'undefined' ) {

              Progress.fail();
              alertError( response.data.errors );

              if ( response.data.error_details ) {

                Account.errors = response.data.error_details.messages;
                Account.error_targets = response.data.error_details.targets;
              
              } else {
                
                Account.errors = {};
                Account.error_targets = {};

              }

            } else {

              Progress.finish();;
              Account.errors = {};
              Account.error_targets = {};

              Account.dirty = false;

            }

          },

          function () {

            Progress.fail();
            alertError( general_error_failure );

          }

        );

      },

      changePassword : function () {

        Progress.start();;
        axios.post( '/api/account/password/change', Account.account ).then( 

          function ( response ) {

            if ( typeof response.data.errors !== 'undefined' ) {

              Progress.fail();
              alertError( response.data.errors );

              if ( response.data.error_details ) {

                Account.errors = response.data.error_details.messages;
                Account.error_targets = response.data.error_details.targets;
              
              } else {
                
                Account.errors = {};
                Account.error_targets = {};

              }

            } else {

              Progress.finish();;
              Account.errors = {};
              Account.error_targets = {};

              Account.dirty = false;

            }

          },

          function () {

            Progress.fail();
            alertError( general_error_failure );

          }

        );

      },

      setAccountEditTab : function (tab) {
				Account.vars.account_edit_tab = tab;
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

            Account.refresh();

        } catch( e ) {
            
        }

      },

  },

  computed: {},
    

  watch : {

    account : {

      handler : function (val, oldVal) {
  
        Account.dirty = true;
      
      },
    
    	deep: true

  	},

    avatar : {

      handler : function (val, oldVal) {
      
        var finished = false;

        for ( var f = 0; f < Account.avatar.files.length; f++ ) {

          if ( Account.avatar.files[f].success ) {

            finished = true;
            Account.avatar.files.splice( f, 1 );

          }

        }

        if ( finished ) {

          Account.fetchAccount();

        }
      
      },

      deep : true

    }

	},

	mounted () {
	}

});

window.Account = Account;
Account.fetchAccount();
