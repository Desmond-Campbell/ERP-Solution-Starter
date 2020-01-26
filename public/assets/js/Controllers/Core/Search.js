import Vue from 'vue';
import axios from 'axios';

export const Search = new Vue({

	el : '#vSearch',
	data : { 
			
				searching : false,
				results : [],
				q : '',
				suggestions : null,
				paging : { page : 1, limit : 15, total : 0 },
			
			},
	
	methods : {

		query : function (keywords) {

			Search.q = keywords;
			Search.searching = true;

			Progress.start();
			axios.get( '/api/search?q=' + Search.q + '&page=' + Search.paging.page ).then( 

				function ( response ) {

					Progress.finish();
					Search.searching = false;

					Search.results = response.data.results;
					Search.paging = response.data.paging;
					Search.suggestions = response.data.suggestions;

				},

				function () {

					Progress.fail();

					Search.searching = false;

					alertError( general_error_failure );

				}

			);

		},

		pageChange (page) {

			Search.paging.page = page;
			Search.query(Search.q);

		},

	},

	computed: {

  },

	watch : {


  }

});

window.Search = Search;
