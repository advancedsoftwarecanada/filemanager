<?php 
// ===============
//
//	FILE MANAGER EXAMPLE
//
// ===============
// @Author: Andy Normore
// https://github.com/AndyNormore/MoodleFileManager

// The point of this file is to demonstrate how to manage files within Moodle 2.3
// Why? Because file management is incredibly hard for some reason.
// This file is built to run as STANDALONE, no external files or strings. Just 100% easy to understand! (Noob friendly)






// --------------
//
// Standard Moodle Setup
//
// --------------
require_once( '../../config.php' );
global $CFG, $USER, $DB, $OUTPUT;

$PAGE->set_url('/local/filemanager/index.php');
require_login();

$PAGE->set_pagelayout( 'admin' );
$context = get_context_instance( CONTEXT_COURSE, SITEID );
$PAGE->set_context( $context );

$PAGE->set_title( 'Page Title' );
$PAGE->set_heading( 'Page Heading' );
	
//DEFINITIONS
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/completionlib.php');







// ===============
//
//
//	(LIBRARY) this would usually be in an: require('lib.php');
//
//
// ===============

class simplehtml_form extends moodleform {

    function definition() {
        global $CFG;
 
        $mform = $this->_form; // Don't forget the underscore! 
			
		// FILE MANAGER
		$mform->addElement('filemanager', 'attachments', 'File Manager label', null, array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 50, 'accepted_types' => array('*') ));
		
		// Buttons
		$this->add_action_buttons();
	
    }
    
    function validation($data, $files) {
        return array();
    }
}











// ===============
//
//
//	PAGE LOGIC
//
//
// ===============

$mform = new simplehtml_form();



// CONFIGURE FILE MANAGER
// From http://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms#filemanager
if (empty($entry->id)) {
    $entry = new stdClass;
    $entry->id = null;
}
 
$draftitemid = file_get_submitted_draft_itemid('attachments');

file_prepare_draft_area($draftitemid, $context->id, 'local_filemanager', 'attachment', $entry->id, array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50));
 
$entry->attachments = $draftitemid;
 

// Set form data
$mform->set_data($entry);



// ===============
//
//
//	PAGE OUTPUT
//
//
// ===============
echo $OUTPUT->header('File Manager', 'File Manager', build_navigation(array(array('name'=>'File Manager','link'=>'','type'=>'misc'))));


// ----------
// Form Submit Status
// ----------

if ($mform->is_cancelled()) { 
	// CANCELLED
	echo '<h1>Cancelled</h1>';
    echo '<p>Handle form cancel operation, if cancel button is present on form<p>';
} else if ($fromform = $mform->get_data()) {
	 // SUCCESS
	echo '<h1>Success!</h1>';
	echo '<p>In this case you process validated data. $mform->get_data() returns data posted in form.<p>';
	echo "<p><center><a href='$CFG->wwwroot/local/filemanager'>Click here to return and see your File Manager!</a></center><p>";
} else { 
	// FAIL / DEFAULT
	echo '<h1>Default / Fail</h1>';
	echo '<p>This is the form first display OR "errors"<p>';
	$mform->display();
}




echo $OUTPUT->footer();

