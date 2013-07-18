<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Language pack for Andresco strings.
 * 
 * Enhanced by Androgogic Pty Ltd
 * http://www.androgogic.com/
 * 
 * @author		Praj Basnet
 * @since		2.2
 * 
 **/

// Standard Andresco Strings

$string['alfresco_url'] = 'Alfresco Repository URL';
$string['alfrescourltext'] = 'Alfresco API url format is http://server:port/alfresco/api';
$string['alfresco:view'] = 'View andresco repository';
$string['configplugin'] = 'Andresco configuration';
$string['notitle'] = 'notitle';
$string['password'] = 'Password';
$string['pluginname_help'] = 'Moodle-Alfresco Integration';
$string['pluginname'] = 'Andresco repository';
$string['soapmustbeenabled'] = 'SOAP extension must be enabled for andresco plugin';
$string['username'] = 'User name';

// Repository Settings

$string['connection_username'] = 'Connection User';
$string['connection_password'] = 'Connection Password';
$string['connection_password_encrypted'] = 'Password Encrypted?';
$string['connection_password_help'] = 'To change, clear the connection password, re-type set "Password has been encrypted" to no.';

$string['test_connection'] = 'Test Connection Settings';

$string['starting_node_uuid'] = 'Starting Node UUID';
$string['starting_node_uuid_help'] = 'UUID of the Node to start at in the Alfresco Repository. 
									  Blank means default to "Company Home". All searches are
									  restricted to content within the starting node and its children.';

$string['content_access_method'] = 'Content Access Method';

$string['andresco_auth'] = 'Auth Script';
$string['andresco_auth_help'] = 'Specify the name and relative path to the andresco authentication script 
								 that should be appended to the base Alfresco URL (e.g. auth.php)';

$string['copy'] = 'Copy (allow download of content into Moodle or linking to content) ';
$string['link'] = 'Link (allow linking to content only)';

$string['versinoning_strategy'] = 'Versioning strategy';
$string['updatetonew'] = 'All links automatically update to new version';
$string['pointpermanently'] = 'All links point to selected version permanently';
$string['userupdate'] = 'User chooses - default is auto update to new version';
$string['userpoint'] = 'User chooses - default is selected version permanently';

$string['unable_to_access_repository'] = 'Unable to access Alfresco. Please review repository settings and Alfresco status.';
$string['upload_file'] = 'Upload File';
$string['upload_folder'] = 'Create Folder';

// Moodle 2.3+ Upload Form Renderer Language strings (files/renderer.php)
$string['uploadfilename'] = 'Filename';
$string['uploadfoldername'] = 'Folder name';
$string['uploadtitle'] = 'Title';
$string['uploaddescription'] = 'Description';
$string['uploadfolder'] = 'Create this folder';
$string['contenttype'] = 'Content Type';

// Version information

$androgogic_alfresco_link = '<a href="http://www.androgogic.com/alfresco" target="_blank">Androgogic\'s Moodle-Alfresco Integration</a>';
$string['version_information'] = 'This is version 1.6.3 of Andresco - ' . $androgogic_alfresco_link;
$string['view_on_share'] = 'View on alfresco share';

//Error Messages
$string['token_expire_error'] = 'Alfresco session expired';
$string['readonly_message'] = 'Alfresco is in readonly mode.';

$string['instancedisabled'] = 'Instance disabled';
$string['instanceenabled'] = 'Instance enabled';
$string['confirmdisable'] = 'Please confirm that this Alfresco repository should be disabled.';
$string['confirmenable'] = 'Please confirm that this Alfresco repository should be enabled.';
$string['disablerepository'] = 'Disable repository';
$string['enablerepository'] = 'Enable repository';
$string['actionrepository'] = 'Enable/Disable';

