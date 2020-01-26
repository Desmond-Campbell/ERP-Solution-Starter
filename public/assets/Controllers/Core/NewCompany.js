
export const NewCompany = new Vue({

	el : '#vNewCompany',
	data : { name : '' },
	methods : {

		createCompany : function () {

			Progress.start();
			axios.post( '/api/companies/create', { 'name' : this.name } ).then( 

				function ( response ) {

					if ( typeof response.data.errors !== 'undefined' ) {

						Progress.fail();
						alertError( response.data.errors );

					} else {

						if ( typeof response.data.id === 'undefined' ) {

							Progress.fail();
							alertError( general_error );

						} else {

							Progress.finish();
							location.assign( '/companies' );

						}

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

window.NewCompany = NewCompany;