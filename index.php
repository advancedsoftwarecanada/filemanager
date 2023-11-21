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
global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url(new moodle_url('/local/filemanager/index.php'));
require_login();


// Choose the most appropriate context for your file manager - e.g. block, course, course module, this example uses
// the system context (as we are in a 'local' plugin without any other context)
$context = context_system::instance();
$PAGE->set_context( $context );
$PAGE->set_title( 'File Manager: Upload A File' );
$PAGE->set_heading( 'File Manager Upload A File' );

//DEFINITIONS
require_once($CFG->libdir.'/formslib.php');

// ===============
//
//
//	(LIBRARY) this would usually be in an: require('lib.php');
//
//
// ===============

class simplehtml_form extends moodleform {

    function definition() {

        $mform = $this->_form; // Don't forget the underscore!
        $filemanageropts = $this->_customdata['filemanageropts'];

        // FILE MANAGER
        $mform->addElement('filemanager', 'attachments', 'File Manager label', null, $filemanageropts);

        // Buttons
        $this->add_action_buttons();
    }
}

// ===============
//
//
// PAGE LOGIC
//
//
// ===============

$filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

$customdata = array('filemanageropts' => $filemanageropts);
$mform = new simplehtml_form(null, $customdata);

// CONFIGURE FILE MANAGER
// From https://moodledev.io/docs/apis/subsystems/form/usage/files
$itemid = 0; // This is used to distinguish between multiple file areas, e.g. different student's assignment submissions, or attachments to different forum posts, in this case we use '0' as there is no relevant id to use

$draftitemid = file_get_submitted_draft_itemid('attachments');

// Copy all the files from the 'real' area, into the draft area
file_prepare_draft_area($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);

// Prepare the data to pass into the form - normally we would load this from a database, but, here, we have no 'real' record to load
$entry = new stdClass();
$entry->attachments = $draftitemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it

// Set form data
$mform->set_data($entry);

// ===============
//
//
// PAGE OUTPUT
//
//
// ===============
echo $OUTPUT->header();

echo "<a href='/local/filemanager/index.php'><input type='button' value='Manage Files'></a>";
echo "<a style='padding-left:10px' href='/local/filemanager/view.php'><input type='button' value='View Files'></a>";
echo "<br /><br /><br />";

// ----------
// Form Submit Status
// ----------
if ($mform->is_cancelled()) {
    // CANCELLED
    echo '<h1>Cancelled</h1>';
    echo '<p>Handle form cancel operation, if cancel button is present on form<p>';
    echo '<p>You can now click "View Files" to see the files you have uploaded.<p>';
} else if ($data = $mform->get_data()) {
    // SUCCESS
    echo '<h1>Success!</h1>';
    echo '<p>In this case you process validated data. $mform->get_data() returns data posted in form.<p>';

    // Save the files submitted
    file_save_draft_area_files($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);

} else {
    // Default view
    // Errors view
    $mform->display();
}



// ----------
// Footer
// ----------
echo $OUTPUT->footer();