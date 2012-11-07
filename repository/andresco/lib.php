<?php

/**
 * Andresco Repository Library
 *
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 * @since		2.0
 * 
 * Note: this code originated from the Moodle delivered repository/alfresco/lib.php
 * and has been enhanced significantly. Changes are tagged accordingly with
 * BEGIN ANDERSCO ... END ANDRESCO comments.
 *
 **/

class repository_andresco extends repository {
    private $ticket = null;
    private $user_session = null;
    private $store = null;
    private $alfresco;

    public function __construct($repositoryid, $context = SYSCONTEXTID, $options = array()) {
        global $SESSION, $CFG;
        parent::__construct($repositoryid, $context, $options);
        $this->sessname = 'alfresco_ticket_'.$this->id;
        if (class_exists('SoapClient')) {
            require_once($CFG->libdir . '/alfresco/Service/Repository.php');
            require_once($CFG->libdir . '/alfresco/Service/Session.php');
            require_once($CFG->libdir . '/alfresco/Service/SpacesStore.php');
            require_once($CFG->libdir . '/alfresco/Service/Node.php');
            // setup alfresco
            $server_url = '';
            if (!empty($this->options['alfresco_url'])) {
                $server_url = $this->options['alfresco_url'];
            } else {
                return;
            }
            $this->alfresco = new Alfresco_Repository($this->options['alfresco_url']);
			
			// BEGIN: Andresco
            //$this->username   = optional_param('al_username', '', PARAM_RAW);
            //$this->password   = optional_param('al_password', '', PARAM_RAW);
			                        
            // Check connection username has been provided            
            if (!empty($this->options['connection_username'])) {
            	$this->username = $this->options['connection_username'];		
            }
			else {
				return;
			}            
			
            // Check connection password has been provided
            if (!empty($this->options['connection_password'])) {
            	$connection_password = $this->options['connection_password'];
            	$this->password = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $CFG->passwordsaltmain, base64_decode($connection_password), MCRYPT_MODE_ECB);
            }
			else {
				return;
			}						
						
			// END: Andresco
			
            try{
            	// BEGIN: Andresco
            	// Re-enable to repository to try connectivity
            	$this->disabled = FALSE;
            	// END: Andresco
				
                // deal with user logging in
                if (empty($SESSION->{$this->sessname}) && !empty($this->username) && !empty($this->password)) {

                    // BEGIN: Andresco 
                    // Test we can get to the Alfresco URL, otherwise, throw exception otherwise pages
                    // will not render correctly in Moodle!
    
                    $c = curl_init();
                    curl_setopt($c, CURLOPT_URL, $this->options['alfresco_url']);
                    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
					if ($this->options['use_ssl'] == 1) {
						// Additional cURL options for SSL
						curl_setopt($c, CURLOPT_SSLVERSION, 3);
						curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
					}
                    $connection_result = curl_exec($c);
                    curl_close($c);

                    if (strpos($connection_result, 'Axis HTTP Servlet')) {
                    $this->ticket = $this->alfresco->authenticate($this->username, $this->password);
                    $SESSION->{$this->sessname} = $this->ticket;
                    }
                    else {
                        // Problem getting to the repository. Throw an exception./log error.                     
                        throw new Exception('Unable to access Alfresco Repository URL: ' . $this->options['alfresco_url']);
                    }
                    // END: Andresco
                   
                } else {
                    if (!empty($SESSION->{$this->sessname})) {
                        $this->ticket = $SESSION->{$this->sessname};
                    }
                }
                $this->user_session = $this->alfresco->createSession($this->ticket);
                $this->store = new SpacesStore($this->user_session);
            } catch (Exception $e) {
            	// BEGIN: Andresco
            	// There was an exception using the login credentials provided.
            	// Disable the repository as it is not working.
            	$this->disabled = TRUE;
				// END: Andresco
            }
            $this->current_node = null;
        } else {
            $this->disabled = true;
        }
    }

    public function print_login() {
        if ($this->options['ajax']) {
            $user_field = new stdClass();
            $user_field->label = get_string('username', 'repository_alfresco').': ';
            $user_field->id    = 'alfresco_username';
            $user_field->type  = 'text';
            $user_field->name  = 'al_username';

            $passwd_field = new stdClass();
            $passwd_field->label = get_string('password', 'repository_alfresco').': ';
            $passwd_field->id    = 'alfresco_password';
            $passwd_field->type  = 'password';
            $passwd_field->name  = 'al_password';

            $ret = array();
            $ret['login'] = array($user_field, $passwd_field);
            return $ret;
        } else {
            echo '<table>';
            echo '<tr><td><label>'.get_string('username', 'repository_alfresco').'</label></td>';
            echo '<td><input type="text" name="al_username" /></td></tr>';
            echo '<tr><td><label>'.get_string('password', 'repository_alfresco').'</label></td>';
            echo '<td><input type="password" name="al_password" /></td></tr>';
            echo '</table>';
            echo '<input type="submit" value="Enter" />';
        }
    }

    public function logout() {
        global $SESSION;
        unset($SESSION->{$this->sessname});
        return $this->print_login();
    }

    public function check_login() {
        global $SESSION;
        return !empty($SESSION->{$this->sessname});
    }

	/**
	 * Generates a url for the target content in a format that redirects
	 * to the Alfresco Moodle-Authentication script (moodleauth.php)
	 * which takes care of things such as:
	 *  - Authentication ticket generation
	 *  - Verifying access to the content (and restricting access from
	 *	  non-Moodle, untrusted sources).
	 *  - Retrieving the appropriate content (e.g. version).
	 * 
	 * @param		Alfresco node being accessed
	 * @return		Alfresco content URL in Andresco format
	 * 
	 */	
	
    private function get_andresco_auth_url($node) {
				
		$base_url = substr($this->options['alfresco_url'], 0, strpos($this->options['alfresco_url'], '/alfresco/api'));
		$alfresco_script = $this->options['andresco_auth'];
		$filename = urlencode($node->cm_name);

		$target_url = "$base_url/$alfresco_script?uuid=$node->id&filename=$filename";		
		
        return $target_url;
    }

    private function get_url($node) {
        $result = null;
        if ($node->type == "{http://www.alfresco.org/model/content/1.0}content") {
            $contentData = $node->cm_content;
            if ($contentData != null) {
                $result = $contentData->getUrl();
            }
        } else {
            $result = "index.php?".
                "&uuid=".$node->id.
                "&name=".$node->cm_name.
                "&path=".'Company Home';
        }

        // BEGIN: Andresco 

        // Remove the port from the returned URL if it is either 80 or 443 as they 
        // are defaults for the relevant protocols (http/https) and not explicitly 
        // required. This is purely to shorten the length of the URL.

        $result = str_replace(':80/', '/', $result);
        $result = str_replace(':443/', '/', $result);
        
        // Shorten the /download/direct/ part of the URL to /d/d/ as this is an
        // acceptable URL format for alfresco.
        
        $result = str_replace('/download/direct/', '/d/d/', $result);

        // Append the extension of the filename to the end of the URL (after the
        // ticket) if there is one. This is so that Moodle can correctly identitfy
        // the mimetype of the document from the URL.
        
        $url_split_at_ticket = explode('?ticket=', $result);
        // Assumes that extensions are 3 characters long. Otherwise ignores them.
        $extension = substr($url_split_at_ticket[0], strlen($url_split_at_ticket[0]) - 4, 4);

        // If there is an extension, append the extension parameter to the end of the url
        if (substr($extension, 0, 1) == '.') {
            $extension_param = '&ext=' . $extension;
            $result = $result . $extension_param;
            
        }       
        // END: Andresco

        return $result;
    }

    /**
     * Get a file list from alfresco
     *
     * @param string $uuid a unique id of directory in alfresco
     * @param string $path path to a directory
     * @return array
     */
    public function get_listing($uuid = '', $path = '') {
        global $CFG, $SESSION, $OUTPUT;
        $ret = array();
        $ret['dynload'] = true;
        $ret['list'] = array();
        $server_url = $this->options['alfresco_url'];
        $pattern = '#^(.*)api#';
        if ($return = preg_match($pattern, $server_url, $matches)) {
        	// BEGIN: Andresco
        	// Change to point to /share instead of /alfresco
        	$alfresco_link = $matches[1] . 'faces/jsp/dashboards/container.jsp';
        	$share_link = str_replace('/alfresco/', '/share/', $matches[1]) . 'page/site-index';
			// TODO: Option to select alfresco		
			$ret['manage'] = $share_link;
			//$ret['versioning_strategy'] = $this->options['versioning_strategy'];
        	// END: Andresco
        }

        $ret['path'] = array(array('name'=>get_string('pluginname', 'repository_andresco'), 'path'=>''));

        try {
        	
            // BEGIN: Andresco                     	    	      	            
        		
            // Set starting node to specified configuration
			
            if (isset($this->options['starting_node_uuid']) && strlen($this->options['starting_node_uuid']) > 0) {
        		$starting_node = $this->user_session->getNode($this->store, $this->options['starting_node_uuid']);
            }
				
            // If failed to set starting node or not specified in configuration, default to company home
            if (!isset($starting_node)) {
        			$starting_node = $this->store->companyHome;
					}
							        	
            if (empty($uuid)) {
        		// BEGIN: Andresco
        		// Use the starting node set above.            	
				$this->current_node = $starting_node;
				// END: Andresco
            } 
            else {
                $this->current_node = $this->user_session->getNode($this->store, $uuid);
            }
            // END: Andresco
									
            $folder_filter = "{http://www.alfresco.org/model/content/1.0}folder";
            $file_filter = "{http://www.alfresco.org/model/content/1.0}content";

            // top level sites folder
            $sites_filter = "{http://www.alfresco.org/model/content/1.0}sites";
            // individual site
            $site_filter = "{http://www.alfresco.org/model/content/1.0}site";

            foreach ($this->current_node->children as $child)
            {
                if ($child->child->type == $folder_filter or
                    $child->child->type == $sites_filter or
                    $child->child->type == $site_filter)
                {
                	// BEGIN: Andresco                	
                    $ret['list'][] = array('title'=>$child->child->cm_name,
                    	'usertitle' => $child->child->cm_title, 
                        'description' => $child->child->cm_description,
                        'path'=>$child->child->id,
                        // Use the smaller folder image
                        //'thumbnail'=>$OUTPUT->pix_url('f/folder-32') . '',
                        'thumbnail'=>$OUTPUT->pix_url('f/folder') . '',
                        'children'=>array());
                } elseif ($child->child->type == $file_filter) {
                    $ret['list'][] = array('title'=>$child->child->cm_name,
                    	'usertitle' => $child->child->cm_title, 
                        'description' => $child->child->cm_description,
                        // Use smaller file extension icons (not 32px size)
                        //'thumbnail' => $OUTPUT->pix_url(file_extension_icon($child->child->cm_name, 32))->out(false),
                        'thumbnail' => $OUTPUT->pix_url(file_extension_icon($child->child->cm_name))->out(false),
                        'source'=>$child->child->id);
                        // END: Andresco

                }
            }
        } catch (Exception $e) {
            unset($SESSION->{$this->sessname});
			
			// BEGIN: Andresco		
			// Uncomment below to see the exception
			// var_dump($e);
			//$ret = $this->print_login();			
			// END: Andresco
			
        }
		
		// BEGIN: Andresco

		// The following code correctly sets the breadcrumbs by setting the value
		// path/level/name and path/level/path multidimensional array.
		$breadcrumb_array = array();

		// Recursively "walk up" the hierarchy and store the breadcrumbs in an array.
		// This array will be in reverse order with the deepest level first and the
		// the starting node (e.g. company home) last.		
		$breadcrumb_node = $this->current_node;

		while ($breadcrumb_node->id != $starting_node->id) {				
			$breadcrumb = array('name' => $breadcrumb_node->cm_name, 'path' => $breadcrumb_node->id);
			array_push($breadcrumb_array, $breadcrumb);
			$breadcrumb_node = $breadcrumb_node->primaryParent;
		}

		$breadcrumb = array('name' => $starting_node->cm_name, 'path' => $starting_node->id);
		array_push($breadcrumb_array, $breadcrumb);
			
		// Go through the breadcrumb array and set the breadcrumb hierarchy
		// Stop when we reach 0 or the top of the hierarchy.

		$hierarchy_level = 0;
		$breadcrumb_level = sizeof($breadcrumb_array) - 1;
		
		while ($breadcrumb_level >= 0) {
			$ret['path'][$hierarchy_level]['name'] = $breadcrumb_array[$breadcrumb_level]['name'];
			$ret['path'][$hierarchy_level]['path'] = $breadcrumb_array[$breadcrumb_level]['path'];
			$hierarchy_level++;
			$breadcrumb_level--;			
		}
		
		// END: Andresco
		
        return $ret;
    }

    /**
     * Download a file from alfresco
     *
     * @param string $uuid a unique id of directory in alfresco
     * @param string $path path to a directory
     * @return array
     */
    public function get_file($uuid, $file = '') {
        $node = $this->user_session->getNode($this->store, $uuid);
        $url = $this->get_url($node);
        $path = $this->prepare_file($file);
        $fp = fopen($path, 'w');
        $c = new curl;
        $c->download(array(array('url'=>$url, 'file'=>$fp)));
        return array('path'=>$path, 'url'=>$url);
    }

    /**
     * Return file URL
     *
     * @param string $url the url of file
     * @return string
     */
    public function get_link($uuid) {
        $node = $this->user_session->getNode($this->store, $uuid);
		// BEGIN: Andresco
		// Get the URL for Andresco Auth script on Alfresco Server instead of the 
		// actual node URL to allow for authentication and permissions handling.
        $url = $this->get_andresco_auth_url($node);
		// END: Andresco
        return $url;
    }

    public function print_search() {
        $str = parent::print_search();
		// BEGIN: Andresco
		// Hide search options for now
		/*
        $str .= '<label>Space: </label><br /><select name="space">';
        foreach ($this->user_session->stores as $v) {
            $str .= '<option ';
            if ($v->__toString() === 'workspace://SpacesStore') {
                $str .= 'selected ';
            }
            $str .= 'value="';
            $str .= $v->__toString().'">';
            $str .= $v->__toString();
            $str .= '</option>';
        }
        $str .= '</select>';
		*/
		// END: Andresco
        return $str;
    }

    /**
     * Look for a file
     *
     * @param string $search_text
     * @return array
     */
    public function search($search_text) {
		global $OUTPUT; // Andresco
		
        $space = optional_param('space', 'workspace://SpacesStore', PARAM_RAW);        
		$currentStore = $this->user_session->getStoreFromString($space);
		// BEGIN: Andresco
		// Adjust search to be on name
        $nodes = $this->user_session->query($currentStore, '@cm\:name:"' . $search_text . '"');
		// END: Andresco
        $ret = array();
        $ret['list'] = array();
		
		// BEGIN: Andresco
		$folder_filter = "{http://www.alfresco.org/model/content/1.0}folder";
        $sites_filter = "{http://www.alfresco.org/model/content/1.0}sites";
        $site_filter = "{http://www.alfresco.org/model/content/1.0}site";
		// END: Andresco
		
        foreach($nodes as $v) {
			
			// BEGIN: Andresco
			// Correctly display file icons for search. This is broken with the delivered Alfresco
			// repository implementation - use the same logic as in get_listing to determine thumbnail icons
						
			if ($v->type == $folder_filter || $v->type == $sites_filter || $v->type == $site_filter) {
				// Exclude folders from search
				/*
				$ret['list'][] = array(
					'title'=>$v->cm_name, 
					'source'=>$v->id,
					'thumbnail'=>$OUTPUT->pix_url('f/folder') . ''
				);				 
				*/
        }
            else {		
				// Files
				$ret['list'][] = array(
					'title'=>$v->cm_name, 
					'source'=>$v->id,
					'thumbnail'=>$OUTPUT->pix_url(file_extension_icon($v->cm_name))->out(false)
				);
			}
			// END: Andresco
		}	
		
        return $ret;
		
    }

    /**
     * Enable mulit-instance
     *
     * @return array
     */
    public static function get_instance_option_names() {
    	// BEGIN: Andresco
    	// return array('alfresco_url');
        return array(
        	'alfresco_url', 
        	'connection_username', 
        	'connection_password', 
        	'connection_password_encrypted',
			'use_ssl',
        	'starting_node_uuid', 
			'andresco_auth',
            'content_access_method'
        );
		// END: Andresco
    }

    /**
     * define a configuration form
     *
     * @return bool
     */
    public function instance_config_form($mform) {
    	
		global $CFG, $PAGE; // Andresco: Added $PAGE global

        // BEGIN: Andresco
        $PAGE->requires->js('/repository/andresco/js/andresco.js');
        // END: Andresco
		
        if (!class_exists('SoapClient')) {
            $mform->addElement('static', null, get_string('notice'), get_string('soapmustbeenabled', 'repository_alfresco'));
            return false;
        }
        $mform->addElement('text', 'alfresco_url', get_string('alfresco_url', 'repository_andresco'), array('size' => '40'));
        $mform->addElement('static', 'alfreco_url_intro', '', get_string('alfrescourltext', 'repository_andresco'));
        $mform->addRule('alfresco_url', get_string('required'), 'required', null, 'client');
		
		// BEGIN: Andresco
		
		// Connection username
		$mform->addElement('text', 'connection_username', get_string('connection_username', 'repository_andresco'), array('size' => '20'));
		$mform->addRule('connection_username', get_string('required'), 'required', null, 'client');
		
		// Connection password 
		$mform->addElement('password', 'connection_password', get_string('connection_password', 'repository_andresco'), array('size' => '60'));
		// TODO: Fix this to ensure password is provided but only if encrypted flag = No
		//$mform->addRule('connection_password', get_string('required'), 'required', null, 'client');
		$mform->disabledIf('connection_password', 'connection_password_encrypted', 'eq', 1);
				
		// TODO: Confirm that the supplied username and password authenticate. If not do not save.		
				
		if (isset($_POST['connection_password'])) {
			if (isset($CFG->passwordsaltmain)) {
				$salt = $CFG->passwordsaltmain;
				
				if ($_POST['connection_password_encrypted'] == 0) {
					$connection_password = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $_POST['connection_password'], MCRYPT_MODE_ECB);
					// Encode in base64 so it will save without error to database
					$connection_password = base64_encode($connection_password);
					$_POST['connection_password'] = $connection_password;		
					$_POST['connection_password_encrypted'] = 1;
				}
				
			}
			else {
				// TODO: warning about no salt being available, password will not be encrypted
			}
			
		}

		// Connection password encrypted
		$mform->addElement('selectyesno', 'connection_password_encrypted', get_string('connection_password_encrypted', 'repository_andresco'));
		$mform->setDefault('connection_password_encrypted', 0);	
		$mform->addElement('static', 'connection_password_help', '', get_string('connection_password_help', 'repository_andresco'));

		// Use SSL option
		$mform->addElement('selectyesno', 'use_ssl', get_string('use_ssl', 'repository_andresco'));
		$mform->setDefault('use_ssl', 0);	
		$mform->addElement('static', 'use_ssl_help', '', get_string('use_ssl_help', 'repository_andresco'));		
		
        // Test Connection button
        $mform->addElement('static', '', '', '');
        $mform->addElement('button', 'test_connection', get_string('test_connection', 'repository_andresco'));
        $mform->addElement('static', '', '', '');

		// Starting path
		$mform->addElement('text', 'starting_node_uuid', get_string('starting_node_uuid', 'repository_andresco'), array('size' => '50'));			
		$mform->addElement('static', 'starting_node_uuid_help', '', get_string('starting_node_uuid_help', 'repository_andresco'));		
				
		// Andresco Authentication Script
        $mform->addElement('text', 'andresco_auth', get_string('andresco_auth', 'repository_andresco'), array('size' => '50'));           
        $mform->addElement('static', 'andresco_auth_help', '', get_string('andresco_auth_help', 'repository_andresco'));

		// Content access option
		$content_access_methods = array(
			'link' => get_string('link', 'repository_andresco'),
			'copy' => get_string('copy', 'repository_andresco')
		);
		$mform->addElement('select', 'content_access_method', get_string('content_access_method', 'repository_andresco'), $content_access_methods);

		// Version information
        $mform->addElement('static', 'version_information', '', get_string('version_information', 'repository_andresco', '1.5.2'));

		// END: Andresco
		
        return true;
    }

    /**
     * Check if SOAP extension enabled
     *
     * @return bool
     */
    public static function plugin_init() {
        if (!class_exists('SoapClient')) {
            print_error('soapmustbeenabled', 'repository_alfresco');
            return false;
        } else {
            return true;
        }
    }
    public function supported_returntypes() {
        return (FILE_INTERNAL | FILE_EXTERNAL);
    }
	
	// BEGIN: Andresco
	
	/**
	 * Display Upload Form on file picker.
	 * 
	 * @param		None
	 * @return		Displays upload form
	 **/
	
	public function print_upload() {
        global $CFG;
        $ret = array();
        $ret['nologin']  = true;
        $ret['nosearch'] = true;
        $ret['norefresh'] = true;
        $ret['list'] = array();
        $ret['dynload'] = false;        
		$ret['upload'] = array('label'=>get_string('upload_file', 'repository_andresco'), 'id' => 'alfresco-upload-form');
        return $ret;    
	}
	
	/**
	 * Process add file request and upload to the relevant node in Alfresco.
	 * 
	 * @param		Save as filename
	 * @param		Max bytes
	 * @param		Uplaod UUID (UUID of target node where upload is going)
	 * @param		Environment (e.g. is this TinyMCE editor?)
	 * @return		JSON encoded result of file upload
	 */
	
	public function upload($saveas_filename, $maxbytes, $upload_uuid, $env) {

		$file_name = $_FILES['repo_upload_file']['name'];
		$file_tmp = $_FILES['repo_upload_file']['tmp_name'];
		$file_type = $_FILES['repo_upload_file']['type'];
		$file_size = $_FILES['repo_upload_file']['size'];
		
		$file_title = $_POST['title'];
		$file_description = $_POST['description'];
		$file_author = $_POST['author'];
		$file_comment = $_POST['comment'];
		
		// Set the upload node to the node the user requested to upload to
		// This is captured as the node the user is in when they click on 
		// the upload button.		
		$upload_node = $this->user_session->getNode($this->store, $upload_uuid);
		
		// Turn off auto version on the upload node
		$upload_node->cm_autoVersion = false;
				
		$file_exists_as_node = FALSE;
		$upload = NULL;
		
		// Is there already a file with this file name in this folder node?
		foreach ($upload_node->children as $child) {
			$child_node = $child->child;
			if ($file_name == $child_node->cm_name) {
				$file_exists_as_node = TRUE;
				
			$upload = $child_node;	
				
				// Turn off auto version on the upload node
				// auto-versioning breaks creaitng individual versions through upload.
				$upload->cm_autoVersion = false;
					
			break; // Stop looping we have a match
			}
		}
		
		// Mainly to save the fact that auto versioning has been turned off
		$this->user_session->save();
				
		if ($file_exists_as_node == FALSE && !(isset($upload))) {
			// No matching children (files in folder) create a new child and version it
		$upload = $upload_node->createChild('cm_content', 'cm_contains', $file_name);
		}
		
		if ($file_exists_as_node == TRUE) {
			// Get URL for an existing node 
			$url = $this->get_url($upload);	
		}
		
		// Set meta-data
		$upload->cm_name = $file_name;
		$upload->cm_title = $file_title;
		$upload->cm_description = $file_description;
		$upload->cm_owner = $file_author;
		$upload->cm_author = $file_author;
		$upload->cm_creator = $file_author;
		$upload->cm_modifier = $file_author;
		
		// Upload file to alfresco
		$contentData = new ContentData($upload, "cm:content");
		$contentData->mimetype = $file_type;
		$contentData->size = $file_size;
		$contentData->encoding = 'UTF-8';
		$contentData->writeContentFromFile($file_tmp);
		$upload->cm_content = $contentData;
		
		// Save changes		
		$this->user_session->save();

		// Create version after save
		// Note only minor versioning available. Major versioning not implemented yet.
		$upload->createVersion($file_comment, false);
		
		if ($file_exists_as_node == FALSE) {
			// Get URL for a new node
			$url = $this->get_url($upload);
		}	
		
		if ($env == 'url' || $env == 'editor') {
			// Return the URL of the newly uploaded file
	        $link = array();
			$link['file'] = $file_name;
	        $link['type'] = 'link';
			$link['url'] = $url; 
	       	echo json_encode($link);
			die;
		}
		else {
			// Return the file
			return $this->get_file($upload->cm_id);
			echo json_encode($file);
			die;
		}
				
	}
	// END: Andresco
	
}
