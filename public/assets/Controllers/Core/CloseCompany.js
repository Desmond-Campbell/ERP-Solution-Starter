
export const CloseCompany = new Vue({

	el : '#vCloseCompany',
	data : { id : 0 },
	methods : {

		closeCompany : function ( id ) {

			Progress.start();
			axios.post( '/api/companies/close', { 'id' : id } ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

					} else {

						Progress.finish();
						location.assign( '/companies' );

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

window.CloseCompany = CloseCompany;