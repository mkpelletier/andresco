                 _                         
                | |                        
  __ _ _ __   __| |_ __ ___  ___  ___ ___  
 / _` | '_ \ / _` | '__/ _ \/ __|/ __/ _ \ 
| (_| | | | | (_| | | |  __/\__ \ (_| (_) |
 \__,_|_| |_|\__,_|_|  \___||___/\___\___/

Andresco: Moodle-Alfresco Integration by Androgogic

This is open source software released under the GPL3 license.
See LICENSE.TXT for details on that.

Andresco is hosted at Google Code:
http://code.google.com/p/andresco

This branch contains the Andresco repository plugin (/repository) and 
Alfresco-side authentication script (/auth).

Note you also need customised Moodle files from the relevant
ANDRESCO_MOODLE_2X version branch to install Andresco completely.

Pre-requisites
--------------

*  Moodle 2.0x (Moodle 2.0 to 2.4 are supported)
*  Alfresco 4.0x
*  PHP mcrypt library needs to be installed. This is used to encrypt all stored 
   passwords

Install Steps (Moodle)
----------------------

(1) Copy repository/andresco/* to Moodle
(2) Browse to relevant ANDRESCO_MOODLE_2X branch and get the relevant
    customisations and put them in your Moodle instance. See README.TXT
    files in these branches for specific details.

Install Steps (Alfresco)
------------------------

(1) Configure web server (e.g. Apache) with rewrite to redirect requests to
    authorisation script in Virtual Host settings around proxy-ajp e.g:

	# BEGIN: Andresco Authentication Script
	DocumentRoot /var/www/html/andresco
	RewriteEngine On
	RewriteRule ^/auth.php /auth.php
	# END: Andresco Authentication Script

(2) Place all code in /auth in the relevant web root e.g. /var/www/html/andresco
    if following the rewrite above.

(3) Move auth.ini.sample to your Alfresco installation directory. This is for
    security reasons (it contains passwords and should not be in webroot). 
    Note rename this to auth.ini and set the relevant values e.g:
    - base_url
    - username
    - password
    - allowed_hosts (these the full base URLs of the moodle(s) that use Andresco)

(4) Update auth.php and change the following line if the auth.ini file is NOT
    located in /opt/alfresco/auth.ini to the relevant path:

	$auth_config = parse_ini_file('/opt/alfresco/auth.ini');

(5) Test by browsing to your Alfresco base url with /auth.php at the end e.g.
    http://localhost/auth.php

    If all is well, the script will load, but give you the message:
    Andresco Auth Exception: URL not provided.


Please use the Google code project home page to download, provide feedback
and support the project.

This project and its components are:
(c) 2012+ Androgogic Pty Ltd 
http://www.androgogic.com/