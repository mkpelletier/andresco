<?php

/**
 * Andresco Alfresco Tester
 *
 * Tests connectivity to Alfresco based on specified repository settings.
 * Note this script needs to be called from Alfresco and isn't that useful
 * if called by itself.
 * 
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 *
 **/

if (empty($_POST)) {
	echo 'This is not the script you are looking for.';
	die();
}

// The moodle config file is required
require('../../../config.php');

// Start with failed test results (which need to pass to be changed)
$results = array(
	'alfresco_url_test' => 'Unsuccessful',
	'alfresco_login_test' => 'Unsuccessful',
);

// Capture POST variables as regular PHP variables for use in this script
$connection_url = $_POST['alfresco_url'];
$connection_username = $_POST['connection_username'];
$connection_password = $_POST['connection_password'];
$connection_password_encrypted = $_POST['connection_password_encrypted'];

if (isset($connection_url)) {

	// Test connection URL
	$c = curl_init();	
	curl_setopt($c, CURLOPT_URL, $connection_url);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
	$connection_result = curl_exec($c);
	if (curl_error($c)) {
		error_log("Andresco connection " . curl_error($c));
	}
	curl_close($c);

	// Check for the Axis HTTP Servlet text to confirm access to Alfresco API
	if (strpos($connection_result, 'Axis HTTP Servlet')) {
		$results['alfresco_url_test'] = 'Successful';
	
		// Proceed with testing connection username/password
		if ( isset($connection_username) && isset($connection_password) ) {

			// Get the password, decrypt first if encrypted
			if ($connection_password_encrypted == 1) {
				$connection_password = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $CFG->passwordsaltmain, (base64_decode($connection_password)), MCRYPT_MODE_ECB);
				// Remove nulls / EOT from end of decrypted string
				$connection_password = trim($connection_password, "\0\4");
			}

			// Adjust connection URL to get the login API web script URL
			$login_api_url = str_replace('api', 's/api/login', $connection_url);

			// Create JSON data to post to login API web script
			$login_data = json_encode(array(
				'username' => $connection_username,
				'password' => $connection_password
			));

			// Submit request to login API web script (POST with JSON data)
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, $login_api_url);
			curl_setopt($c, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($c, CURLOPT_POSTFIELDS, $login_data);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
		    	'Content-Length: ' . strlen($login_data)
		    ));

			if (curl_error($c)) {
				error_log("Andresco login " . curl_error($c));
			}
			
			$login_result = json_decode(curl_exec($c));
			curl_close($c);

			// Do we have a ticket in the results? If so, connection details are good.
			if (isset($login_result->data->ticket)) {
				$results['alfresco_login_test'] = 'Successful';
			}

			// NOTE: Could validate the ticket here, but figure that a generated ticket
			// indicates that the login to Alfresco works. Worth reviewing. 
		}

	}
}

// Return results
echo json_encode($results);

// End andresco/util/test_connection.php