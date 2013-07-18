/**
 * Andresco Javascript Library
 *
 * Requires the YUI javascript library which is is loaded by Moodle.
 * 
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 *
 **/

// Test the connection settings specified.
// Tests include -
// (1) Confirming the ability to get to the Alfresco API location
// (2) Confirming that user details specified can generate an Alfresco ticket

YUI(M.yui.loader).use('node', 'io', 'json-parse', function(Y) { 

	var test_connection_button = Y.one('#id_test_connection');

	test_connection_button.on('click', function() {

		var alfresco_url = Y.one('input#id_alfresco_url').get('value');
		var connection_username = Y.one('input#id_connection_username').get('value');
		test_alfresco_connection(alfresco_url, connection_username);
		
	});

	var test_alfresco_connection = function(url, user) {
		Y.io('../repository/andresco/util/test_connection.php', {
			method: 'POST',
			form: {
				id: 'mform1', // serialize and POST all form values
				useDisabled: true
			},
			on: {
				complete: function(id, response) {
					outcome = Y.JSON.parse(response.responseText);
					console.log(outcome);			
					if (outcome.alfresco_url_test == 'Successful') {						
						if (outcome.alfresco_login_test == 'Successful') {
							alert('Successfully connected to URL and logged into Alfresco with user: ' + user);
						}
						else {
							alert('Failed to login to Alfresco with user: ' + user);
							// Stop at this first error message.
							return;
						}
					}
					else {
						alert('Failed to connect to URL: ' + url);			
					}
				}
			}
		});
	}

});