import Vue from 'vue';
import axios from 'axios';

export const CloseBranch = new Vue({

	el : '#vCloseBranch',
	data : { id : 0 },
	methods : {

		closeBranch : function ( id ) {

			Progress.start();
			axios.post( '/api/settings/branches/' + id + '/close' ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

					} else {

						Progress.finish();
						location.assign( '/settings/branches' );

					}

				},

				function () {

					Progress.fail();
					alertError( general_error_failure );

				}

			);

		}

	}

});

window.CloseBranch = CloseBranch;