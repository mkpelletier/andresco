<?php

/**
 * Andresco Authentication Script
 *
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 * 
 * NOTE: This file should be installed on the ALFRESCO web server (not Moodle)
 * and requires the Alfresco SDK and a configuration file called auth.ini.
 * See the Andresco wiki for further information.
 **/

// Pull in the Alfresco PHP SDK
require_once "Alfresco/Service/Repository.php";
require_once "Alfresco/Service/Session.php";

global $auth_config;

try {
	// Note that the auth.ini file lives in the Alfresco home directory and NOT where the
	// auth.php script is for security reasons.
	$path_to_config_file = '/opt/alfresco/auth.ini';
	$auth_config = parse_ini_file($path_to_config_file);
	if (empty($auth_config)) {
		throw new Exception ("Unable to open or read Andresco Authentication Configuration File (auth.ini)");
	}
}
catch (Exception $e) {
	echo '<h1>Andresco Authentication</h1>';
	echo '<strong>Exception: Problem with config file: ' . $path_to_config_file . '</strong>';
	echo '<br/><pre>' . $e->getMessage() . '</pre>';
	die();
}

// There are two ways to pass parameters to auth.php:
//  (1) Combination Alfresco Node UUID and Filename to uniquely identify a resource
//  (2) A path to the resource using repository hierarchy/friendly names
// This is an either/or thing - either use option (1) or option (2)

// Option 1: UUID and Filename
$uuid = $_GET['uuid'];
$filename = $_GET['filename'];

// Option 2: Path
$path = $_GET['path'];

// Ensure we have the relevant paramters
if ( (!isset($uuid) || !isset($filename)) && !isset($path) ) {
	echo '<h1>Andresco Authentication</h1>';
	echo '<strong>Exception: Invalid parameters provided:</strong>';
	echo '<ul><pre>';
	echo "<li>UUID=$uuid</li>";
	echo "<li>Filename=$filename</li>";
	echo "<li>Path=$path</li>";
	echo '</pre></ul>';
	die();
}

if ( (empty($uuid) || empty($filename)) && empty($path) ) {
	echo '<h1>Andresco Authentication</h1>';
	echo '<strong>Exception: Empty parameters provided:</strong>';
	echo '<ul><pre>';
	echo "<li>UUID=$uuid</li>";
	echo "<li>Filename=$filename</li>";
	echo "<li>Path=$path</li>";
	echo '</pre></ul>';	
	die();
}

$ticket = generate_alfresco_ticket();

$alfresco_url = build_alfresco_url($uuid, $filename, $path, $ticket);

$is_allowed_host = 'No';
if (isset($_SERVER['HTTP_REFERER'])) {
	$search_hosts = explode(",", $auth_config['allowed_hosts']);
	foreach ($search_hosts as $host) {
		if (strstr($_SERVER['HTTP_REFERER'], $host) !== FALSE) {
			$alfresco_url = build_alfresco_url($uuid, $filename, $path, $ticket);
			$is_allowed_host = 'Yes';
			break;
		}
	}
}
else {
	$alfresco_url = build_alfresco_url($uuid, $filename, $path, '');
}

if (isset($_GET['debug']) && $_GET['debug'] == 1) {
	
	error_log('Andresco Referrer=' . $_SERVER['HTTP_REFERER']);
	
	$debug = '<h1>Andresco Authentication</h1>';
	$debug .= '<strong>Debug Information</strong>';
	$debug .= '<pre>';
	$debug .= '<ul>';
	$debug .= "<li>Base URL=" . $auth_config['base_url'] . "</li>";
	$debug .= "<li>Allowed Hosts=" . $auth_config['allowed_hosts'] . "</li>";
	$debug .= "<li>Username=" . $auth_config['username'] . "</li>";
	$debug .= "<li>Password=That's cheating. You're not getting the password through me!</li>";
	$debug .= "<li>UUID=$uuid</li>";
	$debug .= "<li>Filename=$filename</li>";
	$debug .= "<li>Path=$path</li>";
	$debug .= "<li>Ticket=$ticket</li>";
	$debug .= "<li>Target alfresco URL=$alfresco_url</li>";
	$debug .= "<li>Is source an allowed host=" . (string)$is_allowed_host . "</li>";
	$debug .= '</ul>';
	$debug .= '</pre>';
	$debug .= '<p>Note this script will intentionally not load the content provided with debug on so you can see the script parameters.</p>';
	echo $debug;
	die();
}

header("Location: $alfresco_url");

/**
 * Build Alfresco URL from provided UUID, filename and ticket
 * 
 * @param 		UUID
 * @param		Filename (resource name)
 * @param		Ticket
 * @return		Fully qualified URL to Alfresco resource with ticket
 *
 **/

function build_alfresco_url($uuid, $filename, $path, $ticket = '') {
	
	global $auth_config;
	
	$base_url = $auth_config['base_url'];
	$login = '/share';
	$store = '/alfresco/d/d/workspace/SpacesStore/';
	$pathequals = '/alfresco/d/d?path=';
	$target = '';
	
	if (!empty($uuid) && !empty($filename)) {
		$target = $protocol . $base_url . $store . $uuid . '/' . $filename;
		if (!empty($ticket)) {
			$target .= "?ticket=$ticket";
		}		
	}
	else if (!empty($path)) {
		$target = $protocol . $base_url . $pathequals . $path;
		if (!empty($ticket)) {
			$target .= "&ticket=$ticket";
		}
	}
	
	if (empty($target)) {
		// No target return login to share
		return $protocol . $base_url . $login;
	}	
	else {	
		return $target;
	}
}

/**
 * Generate Alfresco Ticket.
 *
 * Note this is proof of concept only and needs to be expanded
 * to pull these details from a database.
 * 
 * @param 		None
 * @return		Repository object
 *
 **/

function generate_alfresco_ticket() {

	global $auth_config;

	$base_url = $auth_config['base_url'];
	$api = '/alfresco/api';
	
	$repository_url = $base_url . $api;

	$username = $auth_config['username'];;
	$password = $auth_config['password'];
	
	$repository = new Repository($repository_url);
	$ticket = $repository->authenticate($username, $password);
	
	return $ticket;
	
}

// End of andresco/auth.php
