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

$string['use_ssl'] = 'Use SSL';
$string['use_ssl_help'] = 'If you are using SSL (https://) then set this to yes';

$string['test_connection'] = 'Test Connection Settings';

$string['starting_node_uuid'] = 'Starting Node UUID';
$string['starting_node_uuid_help'] = 'UUID of the Node to start at in the Alfresco Repository. Blank means default to "Company Home"';

$string['content_access_method'] = 'Content Access Method';

$string['andresco_auth'] = 'Andresco authentication script';
$string['andresco_auth_help'] = 'Specify the name of the andresco authentication script (e.g. auth.php)';

$string['copy'] = 'Copy (allow download of content into Moodle or linking to content) ';
$string['link'] = 'Link (allow linking to content only)';

$string['versinoning_strategy'] = 'Versioning strategy';
$string['updatetonew'] = 'All links automatically update to new version';
$string['pointpermanently'] = 'All links point to selected version permanently';
$string['userupdate'] = 'User chooses - default is auto update to new version';
$string['userpoint'] = 'User chooses - default is selected version permanently';

$string['unable_to_access_repository'] = 'Unable to access Alfresco. Please review repository settings and Alfresco status.';
$string['upload_file'] = 'Upload File';

// Version information

$androgogic_alfresco_link = '<a href="http://www.androgogic.com/alfresco" target="_blank">Androgogic\'s Moodle-Alfresco Integration</a>';
$string['version_information'] = 'This is version {$a} of Andresco - ' . $androgogic_alfresco_link;
