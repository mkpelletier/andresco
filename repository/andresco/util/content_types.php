<?php

/**
 * Andresco: Content Types
 *
 * Loads content types displayed on the filepicker upload form from the
 * plugin configuration settings.
 * 
 * Part of the Andresco Project
 * http://code.google.com/p/andresco
 * 
 * @copyright 	2012+ Androgogic Pty Ltd
 * @author		Praj Basnet
 *
 **/

defined('MOODLE_INTERNAL') || die();

function andresco_content_types() {
	
	$content_types_list = array();
	
	$content_type = new stdClass();	
	$content_type->key='academic-guides';
	$content_type->value='Academic guides';		
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='academic-guidelines';
	$content_type->value='Academic guidelines';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='assessment-tasks';
	$content_type->value='Assessment tasks';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='audio-file';
	$content_type->value='Audio file';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='genre-exemplar';
	$content_type->value='Genre exemplar';	
	$content_types_list[] = $content_type;		
	
	$content_type = new stdClass();
	$content_type->key='image-file';
	$content_type->value='Image file';	
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='learning-activity';
	$content_type->value='Learning activity';	
	$content_types_list[] = $content_type;	
	
	$content_type = new stdClass();
	$content_type->key='lecture-material';
	$content_type->value='Lecture material';	
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='lesson-plan';
	$content_type->value='Lesson plan';	
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='reports';
	$content_type->value='Reports';	
	$content_types_list[] = $content_type;

	$content_type = new stdClass();
	$content_type->key='student-exemplar';
	$content_type->value='Student exemplar';	
	$content_types_list[] = $content_type;

	$content_type = new stdClass();
	$content_type->key='technology-guides';
	$content_type->value='Technology guides';	
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='transcript';
	$content_type->value='Transcript';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='unit-material';
	$content_type->value='Unit material';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='unit-outline';
	$content_type->value='Unit outline';	
	$content_types_list[] = $content_type;
	
	$content_type = new stdClass();
	$content_type->key='video-file';
	$content_type->value='Video file';	
	$content_types_list[] = $content_type;	

	$content_type = new stdClass();
	$content_type->key='web-link';
	$content_type->value='Web link';	
	$content_types_list[] = $content_type;		
	
	return $content_types_list;

}

// End andresco/util/content-types.php