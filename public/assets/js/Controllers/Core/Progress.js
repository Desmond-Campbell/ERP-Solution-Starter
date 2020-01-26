import Vue from 'vue';
import axios from 'axios';

export const Progress = new Vue({

	el : '#vProgress',
	
	data : { 
					
				},

  	methods : {
  		start : function() {

			this.$Progress.start();
			jQuery('body').css('opacity', '0.7').css('filter', 'alpha(opacity=70)');
  			
  		},

  		finish : function() {

			this.$Progress.finish();
			jQuery('body').css('opacity', '1').css('filter', 'alpha(opacity=100)');

  		},

  		fail : function() {

			this.$Progress.fail();
			jQuery('body').css('opacity', '1').css('filter', 'alpha(opacity=100)');
  		
  		}
  	}

});

window.Progress = Progress;
