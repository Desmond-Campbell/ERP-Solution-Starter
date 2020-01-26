
export const Settings = new Vue({

	el : '#vSettings',
	
	data : { 
					vars : { settings_tab : 'company' },
				},
	
	methods : {

		setSettingsTab : function (tab) {
			Settings.vars.settings_tab = tab;
		},

		refresh : function () {

			try {

				Settings.refresh();

			} catch( e ) {
				
			}

		}

	},

	watch : {

  }

});
window.Settings = Settings;
